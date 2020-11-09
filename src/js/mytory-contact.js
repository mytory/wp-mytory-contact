import Vue from 'vue';

if (document.querySelector('.js-contact-list')) {
    new Vue({
        el: '.js-contact-list',
        methods: {
            remove() {
                console.log('remove!');
            }
        }
    });
}
