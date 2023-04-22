import React, { useEffect, useState } from 'react';
import i18n from '../../i18n';
import user from '../../helpers/user';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEdit, faCalendarAlt, faEye } from '@fortawesome/free-solid-svg-icons';
import history from '../../helpers/history';
import request from '../../helpers/request';
import { useParams } from 'react-router-dom'
import config from '../../config'

const FullAnnouncement = (props) => {

    const {announcementId} = useParams()
    const [announcement, setAnnouncement] = useState(null)

    useEffect(() => {
        request.get(`announcements/${announcementId}`).then(response => {
            setAnnouncement(response.data.data)
        })
    }, [])

    const editAnnouncement = () => {
        history.push(`/edit_announcement/${announcement.id}`)
    }
    return (
        announcement ? 
        <div className={`full-announcement-wrapper container`}>
            <div className="announcement-header">
                <span className='post-date'><FontAwesomeIcon icon={faCalendarAlt} /> {announcement.created_at}</span>
                <h1>{i18n.get_locale_data(announcement, 'title')}</h1>
                
            </div>
            
            <div 
                className={`summary`}
                dangerouslySetInnerHTML={{__html: i18n.get_locale_data(announcement, 'body')}}
            >
            </div>
            <div className='files'>
                <h5>{i18n.t('Συννημένα')}:</h5>
                {
                    announcement.attachments.map(attachment => 
                        <div key={`file-${attachment.id}`} className="file" onClick={() => {
                            window.open(`${config.api_url}announcements/${announcementId}/attachments/${attachment.id}`);    
                        }}>
                            <span className="file-name">{attachment.filename}</span>
                            {
                                attachment.filename.endsWith('.pdf') && 
                                <FontAwesomeIcon icon={faEye} />
                            }
                        </div>
                    )
                }
            </div>
            <div className='announcement-footer'>
                <div className='show-more'>
                    {
                        (user.user && ((user.user.is_author === 1 && user.user.id == announcement.author.id) || user.user.is_admin === 1)) &&
                        <button className="btn btn-secondary" onClick={() => editAnnouncement()}><FontAwesomeIcon icon={faEdit} />
                            <span>{i18n.t('Επεξεργασία')}</span>
                        </button>
                    }
                </div>
            </div>
            
        </div> : ''
    )
}

export default FullAnnouncement;