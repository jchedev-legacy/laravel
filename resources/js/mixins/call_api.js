import api from "~/includes/endpoints";

export default {

    methods: {
        /**
         * Can be overwritten to add some logic to execute before any Api Call
         */
        $_beforeApiCall() {
            // We should use this to add logic specific to a component
        },

        /**
         * Can be overwritten to add some logic to execute after any Api Call
         */
        $_afterApiCall() {
            // We should use this to add logic specific to a component
        },

        /**
         *
         * @param response
         * @param options
         */
        $_onApiSuccess(response, options) {
        },

        /**
         *
         * @param error
         * @param options
         */
        $_onApiError(error, options) {
        },

        /**
         * Call an endpoint. Remember of the format : ([method, params], callOptions)
         * callOptions are:
         * - handleError : false / function
         */
        $_api(action, callOptions) {
            let methodName      = null;
            let methodArguments = [];
            let cancelSource    = api.cancelSource();

            callOptions         = callOptions || {};

            // Look if action is an array ["method", argument 1, argument 2, ...] or just "method". We cannot use ...spread because of the last parameter :(
            if (typeof action === 'object') {
                methodName      = action[0];
                methodArguments = action.slice(1);
            } else {
                methodName      = action;
                methodArguments = [];
            }

            // Retrieve the method that will be called or throw an error

            let method = api[methodName];

            if (method === undefined) {
                throw 'Method ' + methodName + ' is not configured';
            }

            // Check what to do with the attribute "options" (which should always be the last item of the method signature)

            if (method.length !== 0) {
                let methodOptionsArgument = {};

                methodOptionsArgument.cancelToken = cancelSource.token;

                if (methodArguments.length > method.length) {
                    // Why would we have more variables than the method expects? Seems like an error
                    console.warn('there is more parameters than the methods allow');
                } else if (methodArguments.length === method.length) {
                    // "options" has been set already : we merge the new values with the existing ones
                    methodArguments[methodArguments.length - 1] = _.merge(methodOptionsArgument, methodArguments[methodArguments.length - 1]);
                } else {
                    // Add one more argument to the array with the default "options"
                    methodArguments.push(methodOptionsArgument);
                }
            }

            // Generate a uniqueId that will be used to track the request and potentially cancel it

            let requestIdentifier = Math.random().toString(36).substring(7);

            this.apiRequests = _.clone(this.apiRequests);

            this.apiRequests[requestIdentifier] = cancelSource;

            // Retrieve the promise from the api/axios object and handle all the error logic if necessary

            this.$_beforeApiCall();

            let promise = api.call(methodName, ...methodArguments)
                .then((response) => {
                    this.$_onApiSuccess(response, callOptions);

                    return response;
                })
                .catch((error) => {
                    this.$_onApiError(error, callOptions);

                    throw error;
                });

            // Some old version dont handle .finally()... That's the workaround.

            let finalMethod = (data) => {
                delete this.apiRequests[requestIdentifier];

                this.$_afterApiCall();

                return data;
            };

            if (promise.finally) {
                return promise.finally(finalMethod);
            } else {
                return promise.then(finalMethod);
            }
        },

        /**
         *
         */
        $_cancelApiCalls() {
            if (Object.keys(this.apiRequests).length !== 0) {

                // The component is being destroyed, we can cancel all the pending requests
                for (let i in this.apiRequests) {
                    this.apiRequests[i].cancel();
                }
            }

            this.apiRequests = {};
        }

    },

    beforeDestroy() {
        this.$_cancelApiCalls();
    },

    data() {
        return {
            apiRequests: {}
        }
    }
}