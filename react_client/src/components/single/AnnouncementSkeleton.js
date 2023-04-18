import React from 'react';

const AnnouncementSkeleton = (props) => {

    return (
        <div className={`announcement-wrapper announcement-wrapper-skeleton`}>
            <span className='post-date'></span>
            <div className="announcement-header">
                <h5></h5>
            </div>
            <div className="badges">
                {
                    Array.from(Array(3)).map((t, i) => 
                        <span key={i} className='tag-badge'></span>
                    )
                }
            </div>
            <div 
                className={`summary`}
            >
                
            </div>
            <div className='announcement-footer'>
                <div className='show-more'>
                    
                </div>
            </div>
            
        </div>
    )
}

export default AnnouncementSkeleton;