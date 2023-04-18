import React, { useEffect, useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faAngleLeft, faAngleRight } from '@fortawesome/free-solid-svg-icons';
import i18n from '../../i18n';

const Pagination = (props) => {

    return <div className={`pagination-wrapper`}>

            <div onClick={() => props.changePage(Math.max(1, props.page - 1))}><FontAwesomeIcon icon={faAngleLeft} /></div>
            {
              props.page > 3
                ? <div onClick={() => props.changePage(1)}>1</div>
                : ''
            }
            {
              props.page > 2
                ? <div onClick={() => props.changePage(props.page - 2)}>{props.page - 2}</div>
                : ''
            }
            {
              props.page > 1
                ? <div onClick={() => props.changePage(props.page - 1)}>{props.page - 1}</div>
                : ''
            }
            {
              props.pagesCount > 0
                ? <div className="active">{props.page}</div>
                : ''
            }
            {
              parseInt(props.page) + 1 <= props.pagesCount
                ? <div onClick={() => props.changePage(parseInt(props.page) + 1)}>{parseInt(props.page) + 1}</div>
                : ''
            }
            {
              parseInt(props.page) + 2 <= props.pagesCount
                ? <div onClick={() => props.changePage(parseInt(props.page) + 2)}>{parseInt(props.page) + 2}</div>
                : ''
            }
            {
              parseInt(props.page) + 3 <= props.pagesCount
                ? <div onClick={() => props.changePage(parseInt(props.page) + 3)}>{parseInt(props.page) + 3}</div>
                : ''
            }
            
            <div onClick={() => props.changePage(Math.min(props.pagesCount, parseInt(props.page) + 1))}><FontAwesomeIcon icon={faAngleRight} /></div>
        </div> 
}

export default Pagination;