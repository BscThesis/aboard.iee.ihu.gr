import React, { useEffect, useState } from 'react';
import i18n from '../../i18n';
import user from '../../helpers/user';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEdit, faCalendarAlt, faEye, faTrash } from '@fortawesome/free-solid-svg-icons';
import history from '../../helpers/history';
import request from '../../helpers/request';
import { useParams } from 'react-router-dom'
import config from '../../config'
import Swal from 'sweetalert2';
import utils from '../../helpers/utils';

const FullAnnouncement = (props) => {

    const {announcementId} = useParams()
    const [announcement, setAnnouncement] = useState(null)

    useEffect(() => {
        request.get(`announcements/${announcementId}`).then(response => {
            if (response.status === 200) {
                setAnnouncement(response.data.data)
            }
            else if (response.status === 401) {
                utils.redirectToApiSSO()
            }
        })
    }, [])

    const editAnnouncement = () => {
        history.push(`/edit_announcement/${announcement.id}`)
    }

    const deleteAnnouncement = () => {
        Swal.fire({
            title: i18n.t('Πρόκειται να διαγράψετε αυτή την ανακοίνωση'),
            text: i18n.t('Επιθυμείτε να συνεχίσετε;'),
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: i18n.t('Ακύρωση'),
            confirmButtonText: i18n.t('Διαγραφή')
        }).then(answer => {
            if (answer.isConfirmed) {
                request.delete(`announcements/${announcement.id}`).then(response => {
                    if (response.status === 200) {
                        Swal.fire({
                            title: i18n.t('Επιτυχία'),
                            text: i18n.t('Η ανακοίνωση διαγράφηκε επιτυχώς'),
                            icon: 'success',
                        }).then(() => {
                            history.push('/announcements')
                        })
                    }
                    else {
                        Swal.fire({
                            title: i18n.t('Αποτυχία'),
                            text: i18n.t('Κάτι πήγε στραβά. Δοκιμάστε αργότερα'),
                            icon: 'error',
                        })
                    }
                })
            }
        })
    }
    return (
        announcement ? 
        <div className={`full-announcement-wrapper container`}>
            <div className="announcement-header">
                <span className='post-date'>{announcement.author.name} | <FontAwesomeIcon icon={faCalendarAlt} /> {announcement.created_at}</span>
                <h1>{i18n.get_locale_data(announcement, 'title')}</h1>
                
            </div>
            
            <div 
                className={`summary`}
                dangerouslySetInnerHTML={{__html: i18n.get_locale_data(announcement, 'body')}}
            >
            </div>
            <div className="badges">
                {
                    announcement.tags.map((t, i) => {
                        if (t.parent_id) return <span key={`tag-${i}`} className='tag-badge'>{t.title}</span>
                        return ''
                    })
                }
            </div>
            {
                announcement.attachments && announcement.attachments.length > 0 &&
            
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
            }
            <div className='announcement-footer'>
                <div className='show-more'>
                    {
                        (user.user && ((user.user.is_author === 1 && user.user.id == announcement.author.id) || user.user.is_admin === 1)) &&
                        <>
                        <button className="btn btn-danger" onClick={() => deleteAnnouncement()}><FontAwesomeIcon icon={faTrash} />
                            <span>{i18n.t('Διαγραφή')}</span>
                        </button>
                        <button className="btn btn-secondary" onClick={() => editAnnouncement()}><FontAwesomeIcon icon={faEdit} />
                            <span>{i18n.t('Επεξεργασία')}</span>
                        </button>
                        </>
                        
                    }
                </div>
            </div>
            
        </div> : ''
    )
}

export default FullAnnouncement;