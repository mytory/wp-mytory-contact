import Vue from 'vue';
import axios from 'axios';
import qs from 'qs';
import swal from 'sweetalert';

Vue.component('contact-manager', require('./contact-manager').default);

new Vue({
    el: '.js-contact-list',
    data: {
        contactList: mytoryContact.contactList,
        showContactManager: false,
        selectedContact: null
    },
    methods: {
        remove(id) {
            swal({
                title: "정말로 삭제할까요?",
                icon: "warning",
                buttons: {
                    cancel: '아니오',
                    remove: {
                        text: '삭제',
                        className: 'swal-button--danger',
                        value: 'remove'
                    }
                }
            }).then((value) => {
                if (value === 'remove') {
                    axios({
                        method: 'POST',
                        url: ajaxurl,
                        headers: {'content-type': 'application/x-www-form-urlencoded'},
                        data: qs.stringify({id: id, action: 'mytory_contact_remove'})
                    }).then(res => {
                        if (res.data.result === 'success') {
                            this.contactList = this.contactList.filter(item => item.ID !== id);
                        } else {
                            throw '관리자에게 문의하세요.';
                        }
                    }).catch(error => {
                        swal({
                            title: '에러 발생!',
                            text: error,
                            icon: 'error'
                        });
                    });
                }
            });
        }
    }
});