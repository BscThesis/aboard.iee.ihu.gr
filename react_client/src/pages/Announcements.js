import React, { useEffect, useState } from "react";
import Announcement from "../components/single/Announcement";
import Pagination from "../components/single/Pagination";
import SearchParams from "../components/single/SearchParams";
import request from "../helpers/request";
import uriHelper from "../helpers/uri_params";
import i18n from "../i18n";
import cookieHelper from "../helpers/cookie";

import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faListDots, faMagnifyingGlassPlus } from "@fortawesome/free-solid-svg-icons";
import { faThLarge } from "@fortawesome/free-solid-svg-icons";
import AnnouncementSkeleton from "../components/single/AnnouncementSkeleton";
import history from "../helpers/history";

const Announcements = (props) => {
    const [boxView, setBoxView] = useState(cookieHelper.get('list_view') == 0)
    const [announcements, setAnnouncements] = useState([])
    const [announcementsMeta, setAnnouncementsMeta] = useState(null)
    const [fetchedAnnouncements, setFetchedAnnouncements] = useState(false)
    const [showSearch, setShowSearch] = useState(false)
    const [lastUpdatedFilters, setLastUpdatedFilters] = useState(Date.now())

    useEffect(() => {
        
        const onPopStateChange = () => {
            uriHelper.clear()
            uriHelper.init()
            uriHelper.set_component_view('announcements')
            setLastUpdatedFilters(Date.now())
        }

        window.addEventListener("popstate", onPopStateChange)
        uriHelper.init()
        const unmountLocaleChange = i18n.onLanguageChange(() => {
            document.title = i18n.t('announcements_page_title')
        })
        document.title = i18n.t('announcements_page_title')
        uriHelper.set_component_view('announcements')
        // getAnnouncements() will be called from within of search parameters
        return () => {
            window.removeEventListener('popstate', onPopStateChange)
            uriHelper.clear()
            unmountLocaleChange()
        };
    }, [])

    useEffect(() => {
        cookieHelper.set('list_view', boxView ? 0 : 1)
    }, [boxView])

    const changePage = (page) => {
        uriHelper.set('page', page)
        getAnnouncements()
    }

    const changeFilters = (pageReset = true, refreshFilterRender = false) => {
        if (pageReset) 
            uriHelper.set('page', 1)

        if (refreshFilterRender) {
            setLastUpdatedFilters(Date.now())
        }
        else {
            getAnnouncements()
        }

        
    }

    const getAnnouncements = () => {
        setFetchedAnnouncements(false)
        const uri = uriHelper.uri_to_query_string()
        request.get(`announcements${uri}`).then(response => {
            if (response.status === 200) {
                setAnnouncements(response.data.data)
                setAnnouncementsMeta(response.data.meta)
                setFetchedAnnouncements(true)
            }
            
        })
    }

    const propagateAnnouncementFilterChange = () => {
        changeFilters(true, true)
        setShowSearch(true)
    }

    return (
        <div className="container mb-4">
            <div className="main-header">
                <h1>{i18n.t('Ανακοινώσεις')}</h1>
                
                <div className="view-options">
                    <span onClick={() => setBoxView(false)} className={`${boxView ? '' : 'active'}`}><FontAwesomeIcon icon={faListDots}/></span>
                    <span onClick={() => setBoxView(true)} className={`${!boxView ? '' : 'active'}`}><FontAwesomeIcon icon={faThLarge}/></span>
                </div>
            </div>

            <div className="active-filters-wrapper">
                <div className="filters-actions">
                    <button className="btn btn-secondary search-btn" onClick={() => setShowSearch(true)}><FontAwesomeIcon className="fs-20" icon={faMagnifyingGlassPlus}/> {i18n.t('Φίλτρα')}</button>
                </div>
            </div>
            
            <SearchParams 
                onChange={(e) => changeFilters(e)}
                setShow={(show) => setShowSearch(show)}
                show={showSearch}
                triggerFilterUpdate={lastUpdatedFilters}
            />
            <div className={`announcements-wrapper ${boxView ? 'box' : ''}`}>
                {
                    (!fetchedAnnouncements && (!announcements || announcements.length === 0)) &&
                    Array.from(Array(10)).map((t, i) => {
                        return <AnnouncementSkeleton key={i}/>
                    })
                    
                }
                {
                    announcements && announcements.length > 0 &&
                        announcements.map((a, i) => {
                            return <Announcement key={`announcement-${i}`} announcement={a} propagateFilterChange={() => propagateAnnouncementFilterChange()} onDelete={() => getAnnouncements()}/>
                        })
                }

                {
                    (fetchedAnnouncements && (!announcements || announcements.length === 0)) && 
                    <span className="no-announcements-found">{i18n.t('Δεν βρέθηκαν ανακοινώσεις')}</span>
                }
                
                {
                    ((announcements && announcements.length > 0)) && 
                    <Pagination 
                        page={announcementsMeta ? announcementsMeta.current_page : 1}
                        pagesCount={announcementsMeta ? announcementsMeta.last_page : 1}
                        changePage={(page) => changePage(page)}
                    />
                }
                
            </div>
        </div>
    )
}

export default Announcements;