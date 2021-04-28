import axios from "axios";
import ApiResponse from "~/objects/ApiResponse";

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

        return promise.catch((error) => {
            if (error.response) {
                // This error has an associated response
                if (error.response.status === 401) {
                    window.location.href = '/logout';
                } else {
                    throw error.response;
                }

            } else if (axios.isCancel(error)) {
                // This error is only triggered because the api call has been canceled
                throw {canceled: true};

            } else {
                // Other type of error
                throw error;
            }
        });
    },

    /**
     *
     * @returns {CancelTokenSource}
     */
    cancelSource() {
        return axios.CancelToken.source();
    },

    /**
     * Return the url of the API
     * @param endpoint
     * @returns {string}
     */
    url(endpoint) {
        return window.api_url + '/' + endpoint;
    },

    /**
     * Use axios to do a GET method to the API
     * @param url
     * @param options
     * @returns {Promise<AxiosResponse<T>>}
     */
    get(url, options) {
        return axios.get(this.url(url), options)
            .then((response) => {
                return new ApiResponse(response.status, response.data, response.headers);
            });
    },

    /**
     *
     * @param url
     * @param data
     * @param options
     * @returns {Promise<AxiosResponse<T>>}
     */
    post(url, data, options) {
        return axios.post(this.url(url), data, options)
            .then((response) => {
                return new ApiResponse(response.status, response.data, response.headers);
            });
    },

    /**
     *
     * @param url
     * @param data
     * @param options
     * @returns {Promise<AxiosResponse<T>>}
     */
    put(url, data, options) {
        return axios.put(this.url(url), data, options)
            .then((response) => {
                return new ApiResponse(response.status, response.data, response.headers);
            });
    },

    /**
     *
     * @param url
     * @param options
     * @returns {*|Promise.<T>}
     */
    delete(url, options) {
        return axios.delete(this.url(url), options)
            .then((response) => {
                return new ApiResponse(response.status, response.data, response.headers);
            });
    }
}