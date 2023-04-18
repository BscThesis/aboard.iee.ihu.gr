import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faMagnifyingGlassMinus, faMagnifyingGlassPlus, faX } from '@fortawesome/free-solid-svg-icons'
import React, { useEffect, useRef, useState } from "react";
import request from "../../helpers/request";
import Select from 'react-select';
import i18n from "../../i18n";
import uriHelper from "../../helpers/uri_params";

import Flatpickr from 'react-flatpickr'
import "flatpickr/dist/flatpickr.css"
import moment from 'moment'
import useDidMountEffect from "../../helpers/useDidMountEffect"
import CheckTree from "./check_module_v2/CheckTree";
import tagsHelper from "../../helpers/tags_helper";

var wait_timeout;

const SearchParams = (props) => {
    const [tags, setTags] = useState([])
    const [authors, setAuthors] = useState([])
    const [didLoad, setDidLoad] = useState(false)

    const containerRef = useRef(null)

    const [filters, setFilters] = useState({
        tags: {
            type: 'select-multiple',
            value: []
        },
        users: {
            type: 'select-multiple',
            value: []
        },
        title: {
            type: 'input',
            value: ''
        },
        body: {
            type: 'input',
            value: ''
        },
        updatedAfter: {
            type: 'input',
            value: ''
        },
        updatedBefore: {
            type: 'input',
            value: ''
        },
        sortId: {
            type: 'input',
            value: '0'
        },
        perPage: {
            type: 'input',
            value: '10'
        }
    })

    const selectThemeColors = theme => ({
        ...theme,
        control: {
            backgroundColor: '#000',
            boxShadow: '#111'
        },
    
        placeholder: {
            color: '#fff',
        },
    
        input: {
            color: '#fff',
        },
    
        singleValue: {
            color: '#aaa',
        },
    
        option: {
            backgroundColor: '#000',
            backgroundColorFocused: '#111',
            color: '#fff'
        },
    
        menu: {
            backgroundColor: '#000'
        }
    })

    const setFilterValues = (key, e) => {  
        setFilters({
            ...filters,
            [key]: {
                ...filters[key],
                value: e
            }
        })
        
    }

    useDidMountEffect(() => {
        if (typeof wait_timeout !== 'undefined') {
            clearTimeout(wait_timeout)
        }
        wait_timeout = setTimeout(() => {
            Object.entries(filters).forEach(item => {
                const [key, data] = item
    
                if (data.type === 'select-multiple') {
                    if (data.value.length > 0)
                        uriHelper.set(key, data.value.map(v => v.value), true)
                    else
                        uriHelper.unset(key, true)
                } else if (data.type === 'input') {
                    if (data.value.length > 0)
                        uriHelper.set(key, data.value, true)
                    else
                        uriHelper.unset(key, true)
                }
                
            })
            uriHelper.set_uri(true)
            props.onChange(didLoad)

            setDidLoad(true)
        }, 300)
        
    }, [filters])

    useEffect(() => {
        setQueryData()
    }, [props.triggerFilterUpdate])

    async function setQueryData() {
        const temp_tags = await getTags()
        const temp_users = await getAuthors()
        const query_params = uriHelper.getAll()
        
        setFilters({
            tags: {
                type: 'select-multiple',
                value: query_params.tags ? getSelected(temp_tags, query_params.tags, true) : []
            },
            users: {
                type: 'select-multiple',
                value: query_params.users ? getSelected(temp_users, query_params.users) : []
            },
            title: {
                type: 'input',
                value: query_params.title ? query_params.title : ''
            },
            body: {
                type: 'input',
                value: query_params.body ? query_params.body : ''
            },
            updatedAfter: {
                type: 'input',
                value: query_params.updatedAfter ? (query_params.updatedAfter) : ''
            },
            updatedBefore: {
                type: 'input',
                value: query_params.updatedBefore ? (query_params.updatedBefore) : ''
            },
            sortId: {
                type: 'input',
                value: query_params.sortId ? (query_params.sortId) : '0'
            },
            perPage: {
                type: 'input',
                value: query_params.perPage ? (query_params.perPage) : '10'
            }
        })
    }

    async function getTags () {
        return new Promise((res, rej) => {
            request.get('/filtertags').then(response => {
                setTags(response.data)
    
                res(response.data)
            })
        })
    }

    async function getAuthors () {
        return new Promise((res, rej) => {
            request.get('/authors').then(response => {
                setAuthors(response.data)
    
                res(response.data)
            })
        })
    }

    const filterArray = (array, compare, children = false) => {
        let ret = array.filter(t => compare.filter(u => parseInt(u) === parseInt(t.id)).length > 0)
        if (children) {
            array.forEach(a => {
                if (a.children_recursive && a.children_recursive.length > 0) {
                    ret = ret.concat(filterArray(a.children_recursive, compare, true))
                }
            })
        }
        return ret;
    }

    const getSelected = (array, compare, children = false) => {
        if (!Array.isArray(compare)) {
            compare = new Array(compare)
        }

        let ret

        
        ret = filterArray(array, compare, children)

        if (ret && ret.length > 0) {
            const val = ret.map(r => {
                const title = r.title ? r.title : r.name
                return {
                    label: `[${r.announcements_count}] ${title}`,
                    value: r.id
                }
            })
            //console.log(val)
            return val
        }


        return []
    }

    const setCustomDate = (key, date) => {
        const d = `${moment(date).format("YYYY-MM-DDT00:00:00.000")}Z`
        setFilters({
            ...filters,
            [key]: {
                ...filters[key],
                value: d
            }
        })
    }

    const shouldSearchClose = (e) => {
        if (containerRef.current && containerRef.current === e.target && props.show === true) {
            props.setShow(false)
        }
    }

    return (
        <div ref={containerRef} onClick={e => shouldSearchClose(e)} className={`search-params-container ${props.show ? 'show' : ''}`}>
            <div className={`search-params-wrapper`}>
                <div className="search-header">
                    <h3>{i18n.t('Φίλτρα αναζήτησης')}</h3>
                    <FontAwesomeIcon className="close-search-params" onClick={() => props.setShow(false)} icon={faX}/>
                </div>
                

                <div className="form-control">
                    <label htmlFor="search-title">{i18n.t('Αναζήτηση βάσει τίτλου')}</label>
                    <input id="search-title" type="text" value={filters.title.value} onChange={(e) => setFilterValues('title', e.target.value)} />
                </div>

                <div className="form-control">
                    <label htmlFor="search-body">{i18n.t('Αναζήτηση βάσει κειμένου')}</label>
                    <input id="search-body" type="text" value={filters.body.value} onChange={(e) => setFilterValues('body', e.target.value)} />
                </div>

                <div className="form-control">
                    <label htmlFor="search-tags">{i18n.t('Αναζήτηση βάσει tags')}</label>
                    {
                        tags &&
                        <CheckTree 
                            className="react-select"
                            prefix="react"
                            id="search-tags"
                            options={tagsHelper.transformTags(tags)}
                            isMulti
                            placeholder={i18n.t('Επιλέξτε tags')}
                            theme={selectThemeColors}
                            value={filters.tags.value}
                            onChange={(e) => setFilterValues('tags', e)}
                            checkAllByParent={false}
                        />
                        // <Select 
                        //     className="react-select"
                        //     prefix="react"
                        //     id="search-tags"
                        //     options={transformTags(tags)}
                        //     isMulti
                        //     placeholder={i18n.t('Επιλέξτε tags')}
                        //     theme={selectThemeColors}
                        //     value={filters.tags.value}
                        //     onChange={(e) => setFilterValues('tags', e)}
                        // />
                    }
                </div>

                <div className="form-control">
                    <label htmlFor="search-authors">{i18n.t('Καθηγητές')}</label>
                    {
                        authors &&
                        <Select 
                            className="react-select"
                            prefix="react"
                            id="search-authors"
                            options={
                                authors.map(t => {
                                    return {
                                        label: `[${t.announcements_count}] ${t.name}`,
                                        value: t.id
                                    }
                                })
                            }
                            isMulti
                            placeholder={i18n.t('Επιλέξτε καθηγητές')}
                            theme={selectThemeColors}
                            value={filters.users.value}
                            onChange={(e) => setFilterValues('users', e)}
                        />
                    }
                </div>

                <div className="form-control">
                    <label htmlFor="search-authors">{i18n.t('Ημερομηνία από')}</label>
                    <Flatpickr 
                        className='form-date' 
                        value={filters.updatedAfter.value} 
                        onChange={date => setCustomDate('updatedAfter', date[0])} 
                        id='edit-from-picker' 
                        options={{
                            dateFormat: 'd-m-Y'
                        }}
                        style={{
                            borderTopRightRadius: 0,
                            borderBottomRightRadius: 0
                        }}
                    />
                </div>

                <div className="form-control">
                    <label htmlFor="search-authors">{i18n.t('Ημερομηνία έως')}</label>
                    <Flatpickr 
                        className='form-date' 
                        value={filters.updatedBefore.value} 
                        onChange={date => setCustomDate('updatedBefore', date[0])} 
                        id='edit-from-picker' 
                        options={{
                            dateFormat: 'd-m-Y'
                        }}
                        style={{
                            borderTopRightRadius: 0,
                            borderBottomRightRadius: 0
                        }}
                    />
                </div>

                <div className="form-control">
                    <label htmlFor="select-sort">{i18n.t('Ταξινόμηση')}</label>
                    <Select 
                        className="react-select"
                        prefix="react"
                        id="select-sort"
                        options={
                            [
                                {
                                    label: i18n.t('Σημαντικές'),
                                    value: '0'
                                },
                                {
                                    label: i18n.t('Νεότερες'),
                                    value: '1'
                                },
                                {
                                    label: i18n.t('Παλαιότερες'),
                                    value: '2'
                                },
                            ]
                        }
                        placeholder={i18n.t('Επιλέξτε ταξινόμηση')}
                        theme={selectThemeColors}
                        value={filters.sortId.value === '0' ? {
                            label: i18n.t('Σημαντικές'),
                            value: '0'
                        } :filters.sortId.value === '1' ?   {
                            label: i18n.t('Νεότερες'),
                            value: '1'
                        } : {
                            label: i18n.t('Παλαιότερες'),
                            value: '2'
                        }}
                        onChange={(e) => setFilterValues('sortId', e.value)}
                    />
                    
                </div>
                <div className="form-control">
                    <label htmlFor="select-per-page">{i18n.t('Ανά σελίδα')}</label>
                    <Select 
                        className="react-select"
                        prefix="react"
                        id="select-per-page"
                        options={
                            [
                                {
                                    label: '10',
                                    value: '10'
                                },
                                {
                                    label: '20',
                                    value: '20'
                                },
                                {
                                    label: '50',
                                    value: '50'
                                },
                            ]
                        }
                        placeholder={i18n.t('Επιλέξτε εγγραφές ανά σελίδα')}
                        theme={selectThemeColors}
                        value={filters.perPage.value === '10' ? {
                            label: '10',
                            value: '10'
                        } :filters.perPage.value === '20' ?   {
                            label: '20',
                            value: '20'
                        } : {
                            label: '50',
                            value: '50'
                        }}
                        onChange={(e) => setFilterValues('perPage', e.value)}
                    />
                    
                </div>
            </div>
        </div>
    )
}

export default SearchParams