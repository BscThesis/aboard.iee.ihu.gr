import history from "./history"

const valid_variables = [
    'page', 'perPage', 'sortId', 'users', 'tags', 'title', 'body', 'date_from', 'date_to', 'updatedAfter', 'updatedBefore'
]

class UriHelper {
    constructor() {
        
        this.raw_uri = window.location.href
        this.pieces = this.raw_uri.split('/')
        this.url = this.pieces[2]

        //This is only a guess. If you want to be sure then call fn set_component_view(String c)
        this.current_component = this.pieces[3]

        this.mapper = {}
        this.pieces.forEach((piece, index) => {
            if (valid_variables.includes(piece) && this.pieces[index + 1]) {
                const p = decodeURIComponent(this.pieces[index + 1])
                this.mapper[piece] = p.includes(',') ? p.split(',') : p
            } 
        })
    }

    /**
     * Sets the current component view in order to use it on history.push
     * @param {String} c 
     */
    set_component_view(c) {
        this.current_component = c
    }
    /**
     * retrieves the value of the {key} parameter
     * If value is not set then return default_value (null)
     * @param {String} key 
     * @param {mixed} default_value 
     */
    get(key, default_value = null) {
        if (typeof this.mapper[key] !== 'undefined') {
            return this.mapper[key]
        } else {
            return default_value
        }
    }

    /**
     * retrieves all values of uri as object|array
     */
    getAll() {
        return this.mapper
    }

    /**
     * Sets the parameter value
     * Sets the value in the URI if {silent} is false
     * @param {String} key 
     * @param {String} value 
     * @param {boolean} silent default false
     */
    set(key, value, silent = false) {
        this.mapper[key] = value
        if (!silent)
            this.set_uri()
    }

    /**
     * Unsets the parameter if exists
     * Unset the value in the URI
     * @param {String} key 
     * @param {boolean} silent default false
     */
    unset(key, silent = false) {
        delete this.mapper[key];
        if (!silent)
            this.set_uri()
    }

    /**
     * Pushes uri to history
     * @param {boolean} silent default false
     */
    set_uri(silent = false) {
        const uri = this.uri_to_string()
        if (!silent)
            history.push(`/${uri}`)
    }

    /**
     * Clears mapper in case it is used from other components. e.g. Announcements and MyAnnouncements
     */
    clear() {
        this.mapper = []
    }

    /**
     * Transforms the mapper object into an actual uri string
     */
    uri_to_string() {
        let str = this.current_component
        for(let m in this.mapper) {
            if (typeof this.mapper[m] !== 'undefined') {
                if (Array.isArray(this.mapper[m])) {
                    str += `/${m}/${encodeURIComponent(this.mapper[m].join(','))}`
                } else {
                    str += `/${m}/${encodeURIComponent(this.mapper[m])}`
                }
            }
                
        }

        return str
    }

    /**
     * Transforms the mapper object into a query string
     */
    uri_to_query_string() {
        let str = '?'
        for(let m in this.mapper) {
            if (typeof this.mapper[m] !== 'undefined') {
                if (Array.isArray(this.mapper[m])) {
                    this.mapper[m].forEach(e => {
                        str += `${m}[]=${encodeURIComponent(e)}&`
                    })
                } else {
                    str += `${m}=${encodeURIComponent(this.mapper[m])}&`
                }
                
            }
        }

        return str
    }
}

const uriHelper = new UriHelper()

export default uriHelper