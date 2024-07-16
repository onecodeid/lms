<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-layout row wrap>
                <v-flex xs5>
                    <v-text-field
                        prefix="Rp."
                        label="Nominal Pembayaran"
                        v-model="transfer_amount"
                    ></v-text-field>
                </v-flex>
                <v-flex xs4 pl-2>
                    <common-datepicker
                        label="Tanggal COD"
                        :date="transfer_date"
                        data="0"
                        @change="change_transfer_date"
                        classs=""
                        hints=" "
                        :details="true"
                        :solo="false"
                    ></common-datepicker>
                </v-flex>
                <v-flex xs3>
                    <v-text-field
                        label="Jam COD"
                        placeholder="00:00"
                        mask="##:##"
                        v-model="transfer_time"
                    ></v-text-field>
                </v-flex>
            </v-layout>

            <v-layout row wrap>
                <v-flex xs12>
                    <v-textarea
                        label="Catatan"
                        placeholder="Tuliskan pesan anda disini"
                        v-model="transfer_note"
                        rows="2"
                    ></v-textarea>
                </v-flex>
            </v-layout>
            
        </v-flex>
    </v-layout>
</template>

<script>
module.exports = {
    data () {
        return {
            imageName: ''
        }
    },

    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    computed : {
        accounts () {
            return this.$store.state.payment.accounts
        },

        selected_account : {
            get () { return this.$store.state.payment.selected_account },
            set (v) { this.$store.commit('payment/set_selected_account', v); }
        },

        banks () {
            return this.$store.state.payment.banks
        },

        selected_bank : {
            get () { return this.$store.state.payment.selected_bank },
            set (v) { this.$store.commit('payment/set_selected_bank', v) }
        },

        transfer_date : {
            get () { return this.$store.state.payment.transfer_date },
            set (v) { this.$store.commit('payment/set_common', ['transfer_date', v]) }
        },

        transfer_time : {
            get () { return this.$store.state.payment.transfer_time },
            set (v) { this.$store.commit('payment/set_common', ['transfer_time', v]) }
        },

        transfer_amount : {
            get () { return this.$store.state.payment.transfer_amount },
            set (v) { this.$store.commit('payment/set_common', ['transfer_amount', v]) }
        },

        transfer_note : {
            get () { return this.$store.state.payment.transfer_note },
            set (v) { this.$store.commit('payment/set_common', ['transfer_note', v]) }
        },

        transfer_name : {
            get () { return this.$store.state.payment.transfer_name },
            set (v) { this.$store.commit('payment/set_common', ['transfer_name', v]) }
        },

        btn_confirm_enabled () {
            
            if (!this.selected_order ||
                !this.selected_account ||
                !this.selected_bank ||
                this.transfer_name == '' ||
                this.transfer_date == '' ||
                this.transfer_time == '' ||
                parseFloat(this.transfer_amount) == 0)
                return false
            return true
        },

        receipt_img : {
            get () {
                if (this.$store.state.payment.receipt_img)
                    return this.$store.state.payment.receipt_img
                return ''
            },
            set (v) { this.$store.commit('payment/set_common', ['receipt_img', v]) }
        },

        selected_order () {
            if (!this.$store.state.salesorder.selected_order)
                return {}
            return this.$store.state.salesorder.selected_order
        }
    },

    methods : {
        change_transfer_date(x) {
            this.transfer_date = x.new_date
        },

        save () {
            this.$store.dispatch('payment/save_payment')
        },

        one_money (x) {
            return window.one_money(x)
        },

        pickFile () {
            this.$refs.image.click ()
        },

        onFilePicked (e) {
			const files = e.target.files
			if(files[0] !== undefined) {
                this.imageName = files[0].name
                
				if(this.imageName.lastIndexOf('.') <= 0) {
					return
				}
				const fr = new FileReader ()
                fr.readAsDataURL(files[0])
				fr.addEventListener('load', () => {
                    this.imageUrl = fr.result
                    this.imageFile = files[0] // this is an image file that can be sent to server...
                    this.getBase64(files[0])
				})
			} else {
				this.imageName = ''
				this.imageFile = ''
				this.imageUrl = ''
			}
        },
        
        getBase64(file) {
            let vue = this
            let photo_uri = ''
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
            //   console.log(reader.result);
                photo_uri = reader.result
                vue.receipt_img = photo_uri
                reader.onerror = function (error) {
                    console.log('Error: ', error);
                };
            }

            
        }
    },

    mounted () {
        this.$store.dispatch('payment/search_bank', {})
        this.$store.dispatch('payment/search_account', {})
        // this.$store.dispatch('payment/search_order', {})
    },

    watch : {
        imageName (v, o) {
            if (v == null)
                this.receipt_img = ''
        }
    }
}
</script>