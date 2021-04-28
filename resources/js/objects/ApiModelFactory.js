import ApiModel from "./ApiModel";

const matches = {
    // type : Object
};

export default class ApiModelFactory {

    /**
     *
     * @param data
     * @returns {*}
     */
    static make(data) {
        if (Array.isArray(data)) {
            return this.makeMultiple(data);
        } else {
            return this.makeOne(data);
        }
    }

    /**
     *
     * @param array
     */
    static makeMultiple(array) {
        for (let i in array) {
            array[i] = this.make(array[i]);
        }

        return array;
    }

    /**
     *
     * @param data
     * @returns {*}
     */
    static makeOne(data) {

        if (this.isCompatible(data)) {
            if (data.type && matches[data.type]) {
                return new matches[data.type](data);
            } else {
                return new ApiModel(data);
            }
        }

        return data;
    }

    /**
     *
     * @param data
     * @returns {boolean}
     */
    static isCompatible(data) {
        if (typeof data === 'object' && data !== null) {
            return data.hasOwnProperty('id') && data.hasOwnProperty('attributes');
        }

        return false;
    }
}