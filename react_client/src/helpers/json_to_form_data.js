class JsonToFormDataHelper {
    buildFormData(formData, data, parentKey) {
        if (data && typeof data === 'object' && !(data instanceof Date) && !(data instanceof File)) {
            Object.keys(data).forEach(key => {
                this.buildFormData(formData, data[key], parentKey ? `${parentKey}[${key}]` : key);
            });
        } else {
            if (data !== null) {
                formData.append(parentKey, data);
            }
            // const value = data == null ? '' : data;
        }
    }
      
    jsonToFormData(data) {
        const formData = new FormData();
        
        this.buildFormData(formData, data);
        
        return formData;
    }
}

const jsonToFormDataHelper = new JsonToFormDataHelper()

export default jsonToFormDataHelper