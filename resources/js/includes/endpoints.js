import axios from "axios";

export default {

    /**
     * Method used to call an endpoint AND run the shared logic on the response
     * @param methodName
     * @param methodParams
     * @returns {Q.Promise<any> | * | Promise<T | never>}
     */
    call(methodName, ...methodParams) {
        let method = this[methodName];

        let promise = method.apply(this, methodParams);

        return promise
            .then((response) => {
                return response;
            })
            .catch((error) => {
                let response = error.response;

                if (response.status === 401) {
                    window.location.href = '/logout';
                } else {
                    throw response;
                }
            });
    },

    /**
     * Return the url of the API
     * @param endpoint
     * @returns {string}
     */
    url(endpoint) {
        return '/api/' + endpoint;
    },

    /**
     *
     * @returns {CancelTokenSource}
     */
    cancelSource() {
        return axios.CancelToken.source();
    },


    /**
     * Use axios to do a GET method to the API
     * @param url
     * @param options
     * @returns {Promise<AxiosResponse<T>>}
     */
    get(url, options) {
        return axios.get(this.url(url), options);
    },

    /**
     *
     * @param url
     * @param data
     * @param options
     * @returns {Promise<AxiosResponse<T>>}
     */
    post(url, data, options) {
        return axios.post(this.url(url), data, options);
    },

    /**
     *
     * @param url
     * @param data
     * @param options
     * @returns {Promise<AxiosResponse<T>>}
     */
    put(url, data, options) {
        return axios.put(this.url(url), data, options);
    },

    /**
     *
     * @param url
     * @param options
     * @returns {*|Promise.<T>}
     */
    delete(url, options) {
        return axios.delete(this.url(url), options);
    }
}