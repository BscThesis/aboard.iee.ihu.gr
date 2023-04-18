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
import { faListDots, faThLarge, faPlus } from "@fortawesome/free-solid-svg-icons";

const MyAnnouncements = (props) => {
    const [boxView, setBoxView] = useState(true)
    const [announcements, setAnnouncements] = useState([])
    const [announcementsMeta, setAnnouncementsMeta] = useState(null)

    useEffect(() => {
        document.title = i18n.t('my_announcements_page_title')
        uriHelper.set_component_view('my_announcements')
        // getAnnouncements() will be called from within of search parameters
        
    }, [])

    const changePage = (page) => {
        uriHelper.set('page', page)
        getAnnouncements()
    }

    const changeFilters = (pageReset = true) => {
        if (pageReset) 
            uriHelper.set('page', 1)
        getAnnouncements()
    }

    const getAnnouncements = () => {
        const uri = uriHelper.uri_to_query_string()
        request.get(`announcements/my_announcements${uri}`).then(response => {
            setAnnouncements(response.data.data)
            setAnnouncementsMeta(response.data.meta)
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
            <button className="btn btn-success" onClick={() => {history.push('/add_announcement')}}><FontAwesomeIcon icon={faPlus} /> {i18n.t('Προσθήκη ανακοίνωσης')}</button>
            
            <SearchParams 
                onChange={(e) => changeFilters(e)}
            />
            <div className={`announcements-wrapper ${boxView ? 'box' : ''}`}>
                {
                    (!announcements || announcements.length === 0) &&
                    Array.from(Array(10)).map(i => {
                        return <AnnouncementSkeleton key={i}/>
                    })
                    
                }
                {
                    announcements && announcements.length > 0 &&
                        announcements.map(a => {
                            return <Announcement key={a.id} announcement={a} />
                        })
                }
                {
                    /**
                     *  "current_page": 1,
                        "from": 1,
                        "last_page": 1,
                        "path": "http://127.0.0.1:8000/api/v2/announcements",
                        "per_page": 10,
                        "to": 1,
                        "total": 1
                    */
                    announcementsMeta && 
                    <Pagination 
                        page={announcementsMeta.current_page}
                        pagesCount={announcementsMeta.last_page}
                        changePage={(page) => changePage(page)}
                    />
                }
            </div>
        </div>
    )
}

export default MyAnnouncements;