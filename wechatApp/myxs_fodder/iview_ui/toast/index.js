const default_data = {
    visible: false,
    content: '',
    icon: '',
    image: '',
    
    mask: true,
    type: 'default', // default || success || warning || error || loading
};
let duration_te = 2;
let timmer = null;

Component({
    externalClasses: ['i-class'],

    data: {
        ...default_data
    },

    methods: {
        handleShow (options) {
            const { type = 'default', duration = 2 } = options;
            duration_te = duration
            this.setData({
                ...options,
                type,
                
                visible: true
            });

            const d = duration_te * 1000;

            if (timmer) clearTimeout(timmer);
            if (d !== 0) {
                timmer = setTimeout(() => {
                    this.handleHide();
                    timmer = null;
                }, d);
            }
        },

        handleHide () {
            this.setData({
                ...default_data
            });
        }
    }
});
