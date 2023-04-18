import React, { useEffect, useState } from "react";
import Announcement from "../components/single/Announcement";
import Pagination from "../components/single/Pagination";
import SearchParams from "../components/single/SearchParams";
import request from "../helpers/request";
import uriHelper from "../helpers/uri_params";
import i18n from "../i18n";
import history from "../helpers/history";
import AnnouncementSkeleton from "../components/single/AnnouncementSkeleton";

import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faListDots, faThLarge, faPlus, faMagnifyingGlassPlus } from "@fortawesome/free-solid-svg-icons";

const MyAnnouncements = (props) => {
    const [boxView, setBoxView] = useState(true)
    const [announcements, setAnnouncements] = useState([])
    const [announcementsMeta, setAnnouncementsMeta] = useState(null)
    const [fetchedAnnouncements, setFetchedAnnouncements] = useState(false)
    const [showSearch, setShowSearch] = useState(false)
    const [lastUpdatedFilters, setLastUpdatedFilters] = useState(Date.now())

    useEffect(() => {
        document.title = i18n.t('my_announcements_page_title')
        uriHelper.set_component_view('my_announcements')

        const unmountLocaleChange = i18n.onLanguageChange(() => {
            document.title = i18n.t('my_announcements_page_title')
        })

        return () => {
            uriHelper.clear()
            unmountLocaleChange()
        };
        // getAnnouncements() will be called from within of search parameters
        
    }, [])

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
        request.get(`announcements/my_announcements${uri}`).then(response => {
            setAnnouncements(response.data.data)
            setAnnouncementsMeta(response.data.meta)
            setFetchedAnnouncements(true)
        })
    }

    return (
        <div className="container mb-4">
            <div className="main-header">
                <h1>{i18n.t('Οι ανακοινώσεις μου')}</h1>
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

            <button className="btn btn-success" onClick={() => {history.push('/add_announcement')}}><FontAwesomeIcon icon={faPlus} /> {i18n.t('Προσθήκη ανακοίνωσης')}</button>
            
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
                        announcements.map(a => {
                            return <Announcement key={a.id} announcement={a} propagateFilterChange={() => changeFilters(true, true)} />
                        })
                }
                {
                    (fetchedAnnouncements && (!announcements || announcements.length === 0)) && 
                    <span className="no-announcements-found">{i18n.t('Δεν βρέθηκαν ανακοινώσεις')}</span>
                }
                
                {
                    (fetchedAnnouncements && (announcements && announcements.length > 0)) && 
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

export default MyAnnouncements;