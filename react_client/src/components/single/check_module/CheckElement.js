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
            ref: inputRef
        })
    }
    
    useEffect(() => {
        if (props.checked) {
            inputRef.current.checked = props.checked

            props.onChange({
                label: props.name,
                value: props.value,
                ref: inputRef
            },
            false, false
            )
        }

        props.onload({
            label: props.name,
            value: props.value,
            ref: inputRef
        })
        
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
            <input type="checkbox" ref={inputRef} onChange={() => onchange()} /> 
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