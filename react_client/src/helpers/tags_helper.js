class TagsHelper {
    /**
     * Transform tags into an object that can be iterated by tree modules
     * @param {Object} tags 
     * @param {String} children_index 
     * @returns {Object}
     */
    transformTags = (tags, children_index = 'children_recursive') => {
        return tags.map(t => {
            const label = t.announcements_count ? `[${t.announcements_count}] ${t.title}` : t.title 

            if (t[children_index] && t[children_index].length > 0) {
                return {
                    label: label,
                    value: t.id,
                    parent: t.parent_id ? t.parent_id : null, 
                    children: this.transformTags(t[children_index], children_index)
                }
            } else {
                return {
                    label: label,
                    value: t.id,
                    parent: t.parent_id ? t.parent_id : null
                }
            }
            
        })
    }

    findOption(value, options) {
        
        const found = options.filter(o => o.value === value)

        if (found.length > 0) {
            return found[0]
        }

        let recursive_search_result = false;
        options.forEach(o => {
            if (o.children && o.children.length > 0) {
                let search = this.findOption(value, o.children)
                if (search) {
                    recursive_search_result = search
                    return
                }
            }
        })

        return recursive_search_result
    }
}

const tagsHelper = new TagsHelper

export default tagsHelper