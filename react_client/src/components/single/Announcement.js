import React, { useEffect, useState } from 'react';
import i18n from '../../i18n';
import user from '../../helpers/user';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faAngleRight, faEdit, faFileCirclePlus, faTrash, faThumbTack } from '@fortawesome/free-solid-svg-icons';
import history from '../../helpers/history';
import { Link } from 'react-router-dom';
import uriHelper from '../../helpers/uri_params';
import Swal from 'sweetalert2';
import request from '../../helpers/request';

const Announcement = (props) => {

    useEffect(() => {
        
    }, [])

    const onTagClick = (tag) => {
        const tags = uriHelper.get('tags', [])
        if (tags.indexOf(tag.id) == -1) {
            tags.push(tag.id)
            uriHelper.set('tags', tags)
        }

        if (typeof props.propagateFilterChange === 'function') {
            props.propagateFilterChange()
        }
    }

    const onAuthorClick = (author) => {
        const authors = uriHelper.get('users', [])
        if (authors.indexOf(author.id) == -1) {
            authors.push(author.id)
            uriHelper.set('users', authors)
        }
        
        if (typeof props.propagateFilterChange === 'function') {
            props.propagateFilterChange()
        }
    }

    const editAnnouncement = () => {
        history.push(`/edit_announcement/${props.announcement.id}`)
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
                request.delete(`announcements/${props.announcement.id}`).then(response => {
                    if (response.status === 200) {
                        if (props.onDelete) {
                            props.onDelete()
                        }
                    }
                })
            }
        })
    }

    return (
        <div className={`announcement-wrapper`}>
            {
                props.announcement.is_pinned === 1 && new Date(props.announcement.pinned_until) >= new Date() &&
                <FontAwesomeIcon icon={faThumbTack} className='pinned-icon'/>
            }
            <div 
                className={`announcement-meta`}
            >
                <span className='author' onClick={() => onAuthorClick(props.announcement.author)}>{props.announcement.author.name}</span>
                <span className='post-date'>{props.announcement.created_at}</span>
            </div>
            
            <div className="announcement-header">
                <Link to={`/announcements/${props.announcement.id}`}><h5>{i18n.get_locale_data(props.announcement, 'title')}</h5></Link>
            </div>
            <div className="badges">
                {
                    props.announcement.tags.map((t, i) => {
                        if (t.parent_id) return <span key={`tag-${i}`} className='tag-badge' onClick={() => onTagClick(t)}>{t.title}</span>
                        return ''
                    }
                        
                    )
                }
            </div>
            <div 
                className={`summary`}
            >
                {i18n.get_locale_data(props.announcement, 'preview')}...
            </div>
            
            <div className='announcement-footer'>
                <div>
                {
                    (props.announcement.attachments && props.announcement.attachments.length > 0) &&
                    <Link to={`/announcements/${props.announcement.id}`} className="btn btn-secondary round"><FontAwesomeIcon icon={faFileCirclePlus} /></Link>
                }
                </div>
                <div className='show-more'>
                    {
                        (user.user && ((user.user.is_author === 1 && user.user.id == props.announcement.author.id) || user.user.is_admin === 1)) &&
                        <>
                            <button className="btn btn-danger" onClick={() => deleteAnnouncement()}><FontAwesomeIcon icon={faTrash} /></button>
                            <button className="btn btn-secondary" onClick={() => editAnnouncement()}><FontAwesomeIcon icon={faEdit} /></button>
                        </>
                    }
                    <Link to={`/announcements/${props.announcement.id}`} className="btn btn-secondary round"><FontAwesomeIcon icon={faAngleRight} /></Link>
                </div>
            </div>
            
        </div>
    )
}

export default Announcement;