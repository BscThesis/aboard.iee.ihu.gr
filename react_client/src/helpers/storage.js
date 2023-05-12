const storage = {
  onChangeFns: [],
  onChange(fn) {
    this.onChangeFns.push(fn);
  },
  onceChange(fn) {
    fn.once = true;
    this.onChangeFns.push(fn);
  },
  get(key, defaultValue = null) {
    if (typeof this[key] !== 'undefined') {
      return this[key];
    }

    return defaultValue;
  },
  set(key, value) {
    this[key] = value;
    this.onChangeFns.forEach((fn) => {
      fn(key, value);
      if (fn.once) {
        this.onChangeFns.splice(this.indexOf(fn), 1);
      }
    });
  },
};
  window.storage = storage;
  
  export default storage;