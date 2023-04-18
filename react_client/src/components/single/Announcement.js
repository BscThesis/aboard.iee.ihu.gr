import React, { useEffect, useState } from 'react';
import i18n from '../../i18n';
import user from '../../helpers/user';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faAngleRight, faEdit, faFileCirclePlus } from '@fortawesome/free-solid-svg-icons';
import history from '../../helpers/history';
import { Link } from 'react-router-dom';
import uriHelper from '../../helpers/uri_params';

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

    return (
        <div className={`announcement-wrapper`}>
            <div 
                className={`announcement-meta`}
            >
                <span className='author' onClick={() => onAuthorClick(props.announcement.author)}>{props.announcement.author.name}</span>
                <span className='post-date'>{props.announcement.created_at}</span>
            </div>
            
            <div className="announcement-header">
                <h5>{i18n.get_locale_data(props.announcement, 'title')}</h5>
            </div>
            <div className="badges">
                {
                    props.announcement.tags.map((t, i) => 
                        <span key={`tag-${i}`} className='tag-badge' onClick={() => onTagClick(t)}>{t.title}</span>
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
                    <Link to={`/announcement/${props.announcement.id}`}className="btn btn-secondary round"><FontAwesomeIcon icon={faFileCirclePlus} /></Link>
                }
                </div>
                <div className='show-more'>
                    {
                        (user.user && user.user.id == props.announcement.author.id) &&
                        <button className="btn btn-secondary" onClick={() => editAnnouncement()}><FontAwesomeIcon icon={faEdit} /></button>
                    }
                    <Link to={`/announcement/${props.announcement.id}`}className="btn btn-secondary round"><FontAwesomeIcon icon={faAngleRight} /></Link>
                </div>
            </div>
            
        </div>
    )
}

export default Announcement;