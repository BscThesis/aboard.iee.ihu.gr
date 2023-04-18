import React, {useEffect, useState, useRef} from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faClose, faAngleDown, faAngleUp } from "@fortawesome/free-solid-svg-icons";
import CheckElement from "./CheckElement";
import './scss/style.css'

const checkItemRefs = []

const CheckTree = (props) => {
    const treeRef = useRef(null)
    const [selectedElements, setSelectedElements] = useState(props.value ? props.value : [])
    const [refElements, setRefElements] = useState([])
    const [searchValue, setSearchValue] = useState('')
    const [showTree, setShowTree] = useState(false)
    const [activeOption, setActiveOption] = useState(null)
    const [optionTree, setOptionTree] = useState(null)
    

    useEffect(() => {
        treeRef.current.addEventListener('click', () => {
            setShowTree(true)
            
        })

        const handleClickOutsideBox = (event) => {
            if (!treeRef.current.contains(event.target)) {
                setShowTree(false)
            }
        }

        document.addEventListener('click', handleClickOutsideBox);
        
        return () => {
            document.removeEventListener('click', handleClickOutsideBox);
        }
    }, [])

    const compareStrings = (a, b) => {
        const normalized_a = a.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase()
        const normalized_b = b.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase()
        return normalized_a.includes(normalized_b)
    }

    const checkShow = (option) => {
        for (let i = 0; i < option.length; i++) {
            if (compareStrings(option[i].label, searchValue)) return {
                showSelf: true,
                showChildren: false,
            }
            else if (option[i].children && option[i].children.length > 0) {
                let children = checkShow(option[i].children)
                if (children.showSelf || children.showChildren) {
                    return {
                        showSelf: true,
                        showChildren: true,
                    }
                }
            }   
        }
        
        
        return {
            showSelf: false,
            showChildren: false,
        }
    }

    const buildOptionTree = (option, selected = [], setShow = false, manual_check = false) => {
        
        const is_checked = selected && selected.length > 0 && selected.filter(s => s.value === option.value).length > 0 || manual_check
        return (
            <CheckElement
                name={option.label}
                value={option.value}
                onChange={(e) => onchange(e)}
                key={option.value}
                show={(setShow || searchValue.length > 0) ? checkShow([option]) : true}
                checked={is_checked}
                onload={(ref) => checkItemRefs.push(ref)}
            >
                {
                    option.children ?
                    option.children.map(o => {
                        return buildOptionTree(o, selected, setShow, (is_checked && props.checkAllByParent === true))
                    }) : ''
                }
            </CheckElement>
        )
    }

    const findOption = (value, options) => {
        
        const found = options.filter(o => o.value === value)

        if (found.length > 0) {
            return found[0]
        }

        let recursive_search_result = false;
        options.forEach(o => {
            if (o.children && o.children.length > 0) {
                let search = findOption(value, o.children)
                if (search) {
                    recursive_search_result = search
                    return
                }
            }
        })

        return recursive_search_result
    }

    const fetchChildren = (options) => {
        let ret = options.map(o => {
            return {
                label: o.label,
                value: o.value
            }
        })

        options.forEach(o => {
            if (o.children && o.children.length > 0) {
                ret = ret.concat(fetchChildren(o.children))
            }
        })

        return ret
    }

    const fetchRefElement = (value) => {
        return checkItemRefs.filter(r => r.value === value)[0]
    }

    const onchange = (e, manual = false, initProps = true) => {
        if (manual) {
            e.ref.current.click()
            return
        }
        let sel = selectedElements
        if (e.ref.current.checked) {
            if (sel.filter(s => s.value === e.value).length === 0) {
                sel.push(e)

                if (props.checkAllByParent) {
                    const choice = findOption(e.value, props.options)
                    
                    if (choice.children) {
                        const children = fetchChildren(choice.children)
                        sel = children.filter(c => sel.filter(s => s.value === c.value).length === 0).concat(sel)
                        sel.forEach(tr => {
                            fetchRefElement(tr.value).ref.current.checked = true
                            tr.ref = fetchRefElement(tr.value)
                        })
                    }
                }
            }
        } else {
            if (props.checkAllByParent) {
                const choice = findOption(e.value, props.options)
                
                if (choice.children) {
                    const children = fetchChildren(choice.children)
                    const to_remove = sel.filter(s => children.filter(c => c.value === s.value).length > 0)
                    to_remove.forEach(tr => {
                        fetchRefElement(tr.value).ref.current.checked = false
                    })
                    sel = sel.filter(s => children.filter(c => c.value === s.value).length === 0)
                }
                
                
            }
            sel = sel.filter(s => s.value !== e.value)

        }
        
        setSelectedElements(sel)
        if (initProps)
            props.onChange(sel)
    }

    useEffect(() => {
        setOptionTree(props.options.map(option => buildOptionTree(option, props.value)))
        setSelectedElements(props.value, false)
        //console.log(props.value)
    }, [props.value])

    

    useEffect(() => {
        setOptionTree(props.options.map(option => buildOptionTree(option, props.value, true)))
    }, [searchValue])

    useEffect(() => {
        setOptionTree(props.options.map(option => buildOptionTree(option, props.value)))
    }, [props.options])

    return <div className="react-check-tree" ref={treeRef}>
        <div className={`tree-controller ${showTree ? 'tree-opened' : ''}`}>
            <div className="tree-controller-container">
            
                {
                    selectedElements.map(e => {
                        return (
                            <span key={`selected_${e.value}`} className="selected-choice" onClick={(event) => {
                                event.stopPropagation()
                                onchange({...e, checked: false}, e.ref)
                            }}>
                                {e.label}
                                <FontAwesomeIcon icon={faClose} />
                            </span>
                        )
                    })
                }
                <input type="text" value={searchValue} placeholder={props.placeholder && selectedElements.length === 0 ? props.placeholder : ''} onChange={(e) => setSearchValue(e.target.value)} />
            </div>
            <div className="tree-controller-indicators">
                <FontAwesomeIcon icon={showTree ? faAngleUp : faAngleDown} onClick={(e) => {
                e.preventDefault()
                e.stopPropagation()

                setShowTree(!showTree)
            }}/>
            </div>
        </div>
        <div className={`tree-root tree-choices ${showTree ? '' : 'tree-choices-hide'}`}>
            {optionTree}
        </div>
    </div>

}

export default CheckTree