import ApiModelFactory from "./ApiModelFactory";

export default class ApiModel {

    /**
     *
     * @param props
     */
    constructor(props) {
        this.set(props);
    }

    /**
     *
     * @returns {*}
     */
    id() {
        return this._id;
    }

    /**
     *
     * @returns {*}
     */
    type() {
        return this._type;
    }

    /**
     *
     * @returns {*|{}}
     */
    getAttributes() {
        return this._attributes;
    }

    /**
     *
     * @param key
     * @param defaultValue
     * @returns {*}
     */
    attribute(key, defaultValue) {
        return _.get(this._attributes, key, defaultValue || null);
    }

    /**
     *
     * @param key
     * @returns {boolean}
     */
    hasAttribute(key) {
        return _.has(this._attributes, key);
    }

    /**
     *
     * @returns {{}|*}
     */
    getRelated() {
        return this._related;
    }

    /**
     *
     * @param key
     * @param defaultValue
     * @returns {*}
     */
    related(key, defaultValue) {
        return _.get(this._related, key, defaultValue || null);
    }

    /**
     * Overwrite the content of the object and could remove all missing attriubtes
     * @param props
     */
    set(props) {
        if (props instanceof ApiModel) {
            props = {
                id:         props.id(),
                type:       props.type(),
                attributes: props.getAttributes(),
                related:    props.getRelated()
            };
        }

        this._id = props.id;

        this._type = props.type;

        this._attributes = props.attributes || {};

        this._related = {};

        for (let i in props.related) {

            let related = props.related[i];

            if (related && related.data !== undefined) {
                related = related.data;
            }

            this._related[i] = ApiModelFactory.make(related);
        }
    }

    /**
     * Only set the defined attributes. Missing ones are NOT deleted
     * @param props
     */
    merge(props) {
        if (props instanceof ApiModel) {
            props = {
                id:         props.id(),
                type:       props.type(),
                attributes: props.getAttributes(),
                related:    props.getRelated()
            };
        }

        if (props.attributes) {
            for (let i in props.attributes) {
                this._attributes[i] = props.attributes[i];
            }
        }

        if (props.related) {
            for (let i in props.related) {
                let related = props.related[i];

                if (related && related.data !== undefined) {
                    related = related.data;
                }

                this._related[i] = ApiModelFactory.make(related);
            }
        }

        return this;
    }

    /**
     *
     * @param key
     * @param value
     * @returns {ApiModel}
     */
    setRelated(key, value) {
        this._related[key] = value;

        return this;
    }

    /**
     *
     * @param key
     * @param value
     * @returns {ApiModel}
     */
    setAttribute(key, value) {
        this._attributes[key] = value;

        return this;
    }
}