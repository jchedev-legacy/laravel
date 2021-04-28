import ApiModelFactory from './ApiModelFactory';

export default class ApiResponse {

    /**
     * Construct a new ApiResponse based on the payload of the axios response
     * @param statusCode
     * @param payload
     * @param headers
     */
    constructor(statusCode, payload, headers) {
        // Http Status code
        this.status = statusCode;

        // Payload.data = data
        this.data = ApiModelFactory.make(payload ? (payload.data || []) : []);

        // Meta is not always returned in the payload
        this.meta = payload ? (payload.meta || {}) : {};

        // Headers of the response
        this.headers = headers;
    }
}