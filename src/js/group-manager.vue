<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <header class="modal-header">
                        <h1 style="float: left; padding: 0;">{{ group.name }}</h1>

                        <div style="float: right;">
                            <button class="button  button-primary" @click="save()" :disabled="isProcessing">
                                저장
                            </button>
                            <button class="button" @click="close" :disabled="isProcessing">
                                취소
                            </button>
                        </div>
                    </header>

                    <div class="modal-body">
                        <div class="o-layout">
                            <div class="o-layout__item  u-6/12">
                                <h2>전체 연락처</h2>
                                <table class="hover-table  u-12/12" style="font-size: 0.9rem;">
                                    <tr v-for="(contact, index) in contactList">
                                        <td>{{ contact.name }}</td>
                                        <td style="font-family: monospace;">{{ contact.phone }}</td>
                                        <td>
                                            <button class="button  button-primary  button-small" @click="add(contact)"
                                                    v-show="!isSelected(contact)">
                                                추가
                                            </button>
                                            <button class="button  button-small" @click="remove(contact)"
                                                    v-show="isSelected(contact)">
                                                빼기
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="o-layout__item  u-6/12">
                                <h2>{{ group.name }} 연락처</h2>
                                <p v-if="groupContactList.length === 0">
                                    {{ group.name }} 그룹에 추가할 연락처를 좌측에서 선택해 주세요.
                                </p>
                                <table class="hover-table  u-12/12" style="font-size: 0.9rem;">
                                    <tr v-for="(contact, index) in groupContactList">
                                        <td>{{ contact.name }}</td>
                                        <td style="font-family: monospace;">{{ contact.phone }}</td>
                                        <td>
                                            <button class="button  button-small" @click="remove(contact)">빼기</button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div style="text-align: center;">
                            <button class="button  button-primary" @click="save()" :disabled="isProcessing">
                                저장
                            </button>
                            <button class="button" @click="close" :disabled="isProcessing">
                                취소
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        name: "group-manager",
        data() {
            return {
                groupContactList: this.selectedGroupContactList,
                isProcessing: false
            }
        },
        methods: {
            add(contact) {
                if (!this.isSelected(contact)) {
                    this.groupContactList.push(contact);
                }
            },
            remove(contact) {
                this.groupContactList = this.groupContactList.filter(item => contact.phone !== item.phone);
            },
            isSelected(contact) {
                return (this.groupContactList.filter(item => item.phone === contact.phone).length > 0);
            },
            save() {
                this.isProcessing = true;
                swal('저장.').then(() => {
                    this.$emit('close');
                });
            },
            close() {
                swal('저장하지 않은 데이터가 날아갑니다. 취소하고 닫을까요?', {
                    buttons: {
                        open: '열어 두기',
                        close: {
                            'text': '닫기',
                            'value': 'close',
                            className: 'swal-button swal-button--confirm swal-button--danger'
                        }
                    }
                }).then(data => {
                    switch (data) {
                        case 'close':
                            this.$emit('close');
                            break;
                        case 'open':
                            break;
                    }
                }).catch(error => {
                    swal(error);
                });

            }
        },
        props: ['group', 'contactList', 'selectedGroupContactList'],
    }
</script>

