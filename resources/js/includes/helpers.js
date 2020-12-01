module.exports = {
    /**
     * Easy way to create a new Cookie
     * @param cname
     * @param cvalue
     * @param exdays
     */
    setCookie(cname, cvalue, exdays) {
        let expires = '';

        if (exdays !== undefined) {
            let date = new Date();

            date.setTime(date.getTime() + ((exdays || 365) * 24 * 60 * 60 * 1000));

            expires = "expires=" + date.toUTCString();
        }

        document.cookie = cname + "=" + cvalue + ";path=/;" + expires + "";
    },

    /**
     * Easy way to retrieve a cookie
     * @param cname
     * @returns {string}
     */
    getCookie(cname) {
        let name          = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca            = decodedCookie.split(';');

        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];

            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }

            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
    },

    /**
     * Define logic to return on a 404 page
     */
    redirectTo404() {
        window.location.href = '/not-found';
    },

    /**
     * Define logic to return on a 403 page
     */
    redirectTo403() {
        window.location.href = '/unauthorized';
    }
};