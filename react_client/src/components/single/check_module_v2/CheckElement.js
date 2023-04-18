import React, {useState, useEffect, useRef} from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faAngleDown, faAngleUp } from "@fortawesome/free-solid-svg-icons";

const CheckElement = (props) => {
    const inputRef = useRef(null)
    const [showTree, setShowTree] = useState(false)

    const onchange = () => {
        props.onChange({
            label: props.name,
            value: props.value,
            parent: props.parent,
        })
    }
    
    useEffect(() => {
        if (props.checked) {
            inputRef.current.checked = props.checked

            props.onChange({
                label: props.name,
                value: props.value,
                parent: props.parent,
                ref: inputRef
            },
            false, false
            )
        }
        
    }, [])

    useEffect(() => {
        if (typeof props.show.showChildren !== 'undefined') {
            setShowTree(props.show.showChildren)
        }
    }, [props])

    const handleClick = () => {
        inputRef.current.click();
    };

    return (
        <div className={`tree-choice ${(props.show === true || props.show.showSelf || props.show.showChildren) ? '' : 'hidden'}`}>
            {
                props.children ? 
                <FontAwesomeIcon className="show-hide-btn" icon={showTree ? faAngleUp : faAngleDown} onClick={() => setShowTree(!showTree)} />
                : ''
            }
            <input type="checkbox" id={props.id} className={props.className} value={props.value} ref={inputRef} onChange={() => onchange()} /> 
            <label onClick={handleClick}>
                {props.name}
            </label>
            {
                props.children ?
                <div className={`tree-choices ${showTree ? '' : 'tree-choices-hide'}`}>
                    {props.children}
                </div> : ''
            }
            
        </div>
    )
}

export default CheckElement