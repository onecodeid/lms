<template>
    <v-card>
        <v-card-title primary-title class="cyan white--text pt-3">
            <h3>INFORMASI</h3>
        </v-card-title>
        <v-card-text>
            <v-card>
                <v-img
                :src="info_img"
                aspect-ratio="2.75"
                v-show="info_img!=''"
                ></v-img>

                <v-card-title primary-title>
                    <div>
                        <h3 class="headline mb-2">{{ info_title }}</h3>
                        <div v-html="info_content"></div>
                    </div>
                </v-card-title>

                <!-- <v-card-actions><v-spacer></v-spacer>
                <v-btn flat color="red">Saya Sudah Baca</v-btn>
                <v-btn flat color="orange" v-show="m.notif_msg_full.length>250">Baca Detail</v-btn>
                </v-card-actions> -->
            </v-card>
        </v-card-text>

        <v-card-actions>
            <v-spacer></v-spacer>  
            <v-btn color="primary" flat @click="dialog=!dialog">Tutup</v-btn>
        </v-card-actions>
    </v-card>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}
</style>
<script>
module.exports = {
    components : {

    },

    data () {
        return {
            
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.infoPreview.dialog },
            set (v) { this.$store.commit('infoPreview/set_common', ['dialog', v]) }
        },

        info_title () {
            return this.$store.state.infoPreview.info_title
        },

        info_content () {
            return this.$store.state.infoPreview.info_content
        },

        info_img () {
            return this.$store.state.infoPreview.info_img
        },

        // info_title : {
        //     get () { return this.$store.state.info_new.info_title },
        //     set (v) { this.$store.commit('info_new/set_common', ['info_title', v]) }
        // },

        // info_content : {
        //     get () { return this.$store.state.info_new.info_content },
        //     set (v) { this.$store.commit('info_new/set_content', v) }
        // },

        // colors () {
        //     return this.$store.state.info_new.colors
        // },

        // selected_color : {
        //     get () { return this.$store.state.info_new.selected_color },
        //     set (v) { this.$store.commit('info_new/set_object', ['selected_color', v]) }
        // },

        // info_img : {
        //     get () {
        //         if (this.$store.state.info_new.info_img)
        //             return this.$store.state.info_new.info_img
        //         return ''
        //     },
        //     set (v) { this.$store.commit('info_new/set_common', ['info_img', v]) }
        // },

        // edate : {
        //     get () { return this.$store.state.info_new.info_edate },
        //     set (v) { this.$store.commit('info_new/set_common', ['info_edate', v]) }
        // },

        // sdate : {
        //     get () { return this.$store.state.info_new.info_sdate },
        //     set (v) { this.$store.commit('info_new/set_common', ['info_sdate', v]) }
        // }
    },

    methods : {
        // save () {
        //     this.$store.dispatch('info_new/save')
        // },

        // insertAtCursor (x) {
        //     let t = this.$el.querySelector('.v-input__control')
        //     console.log(t)
        // },

        // pickFile () {
        //     this.$refs.image.click ()
        // },

        // onFilePicked (e) {

        //     const files = e.target.files
        //     if(files[0] !== undefined) {
        //         this.imageName = files[0].name
                
        //         if(this.imageName.lastIndexOf('.') <= 0) {
        //             return
        //         }
        //         const fr = new FileReader ()
        //         fr.readAsDataURL(files[0])
        //         fr.addEventListener('load', () => {
        //             this.imageUrl = fr.result
        //             this.imageFile = files[0] // this is an image file that can be sent to server...
        //             this.getBase64(files[0])
        //         })
        //     } else {
        //         this.imageName = ''
        //         this.imageFile = ''
        //         this.imageUrl = ''
        //     }
        // },
        
        // getBase64(file) {
        //     let vue = this
        //     let photo_uri = ''
        //     var reader = new FileReader();
        //     reader.readAsDataURL(file);
        //     reader.onload = function () {
        //     //   console.log(reader.result);
        //         photo_uri = reader.result
        //         vue.info_img = photo_uri
        //         reader.onerror = function (error) {
        //             console.log('Error: ', error);
        //         };
        //     }

            
        // },

        // clearImage() {
        //     this.imageUrl = ''
        //     this.imageName = ''
        //     this.imageFile = ''
        //     this.info_img = null
        // },

        // change_edate(x) {
        //     this.edate = x.new_date
        // },

        // change_sdate(x) {
        //     this.sdate = x.new_date
        // }
    },

    mounted () {
        // this.$store.dispatch('info_new/search_color')
    },

    watch : {}
}
</script>