import React, {useRef} from "react";
import { useEffect } from "react";

const Checkbox = (props) => {
    const ref = useRef(null)

    useEffect(() => {
        if (props.checked) {
            ref.current.checked = true
        }
    })

    return (
        <div className="checkbox-wrapper">
            <input ref={ref} type="checkbox" id={props.id ? props.id : `checkbox-elem-${Math.ceil(Math.random() * 10000)}`} onChange={() => props.onChange(ref.current.checked ? 1 : 0)}/>
            <label onClick={() => ref.current.click()}>{props.title}</label>
        </div>
    )
}

export default Checkbox