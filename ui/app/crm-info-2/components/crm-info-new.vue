<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="900px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="cyan white--text pt-3">
                <h3>PENGUMUMAN BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>
                            <v-flex xs6>
                                <v-card style="height:500px">
                                    <v-card-text class="watzap-text-container fill-height">
                                        <v-layout row wrap>
                                            <v-flex xs12 class="px-2">
                                                <v-text-field
                                                    label="Judul"
                                                    v-model="info_title"
                                                >
                                                    <template v-slot:append-outer>
                                                        <v-btn class="ma-0 btn-icon teal accent-4 white--text small" @click="gpt">
                                                            <b>GPT</b>
                                                        </v-btn>
                                                    </template>
                                                </v-text-field>
                                            </v-flex>
                                        </v-layout>
                                        
                                        <v-layout column>
                                            <v-flex>
                                                <v-textarea
                                                    v-model="info_content"
                                                    class="fill-height pa-2"

                                                    label="Konten"
                                                >
                                                </v-textarea>
                                            </v-flex>
                                        </v-layout>

                                        <v-layout row wrap>
                                            <v-flex xs6 pl-2>
                                                <common-datepicker
                                                    label="Tanggal tayang"
                                                    :date="sdate"
                                                    data="0"
                                                    @change="change_sdate"
                                                    classs="mt-0 input-dense"
                                                    hints=""
                                                    :details="false"
                                                    :solo="false"
                                                    v-if="dialog"
                                                ></common-datepicker>
                                            </v-flex>

                                            <v-flex xs6 pr-2>
                                                <common-datepicker
                                                    label="Sampai Tanggal"
                                                    :date="edate"
                                                    data="0"
                                                    @change="change_edate"
                                                    classs="mt-0 ml-1 input-dense"
                                                    hints=""
                                                    :details="false"
                                                    :solo="false"
                                                    v-if="dialog"
                                                ></common-datepicker>
                                            </v-flex>
                                        </v-layout>

                                        <v-layout row wrap>
                                            <v-flex xs12 v-if="(info_img==null||info_img=='')&&!!dialog">
                                                <v-divider class="mt-5 mb-2"></v-divider>
                                                <v-text-field label="Select Image" @click='pickFile' v-model='imageName' prepend-icon='attach_file'></v-text-field>
                                                <input
                                                    type="file"
                                                    style="display: none"
                                                    ref="image"
                                                    accept="image/*"
                                                    @change="onFilePicked"
                                                >
                                                <v-divider class="mt-2 mb-0"></v-divider>
                                            </v-flex>
                                        </v-layout>

                                        

                                        <v-layout row wrap>
                                            <v-flex xs12 style="position:relative" v-show="info_img!=null&&info_img!=''">
                                                <v-img :src="info_img" position="center center" class="ma-0">
                                                </v-img>
                                                <div style="height:50px;background:grey;position:absolute;top:0;width:100%">
                                                    <v-btn color="red" class="btn-icon" @click="clearImage">close</v-btn>
                                                </div>
                                            </v-flex>
                                        </v-layout>
                                    </v-card-text>
                                </v-card>
                            </v-flex>

                            <v-flex xs6 pl-3>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-select
                                            :items="users"
                                            item-value="id"
                                            item-text="text"
                                            label="Penerima"
                                            return-object
                                            multiple
                                            v-model="selected_users"
                                            :value="selected_users"
                                        >
                                            <template
                                                slot="item"
                                                slot-scope="data">

                                                <!-- Divider and Header-->
                                                <template v-if="!!data.item.header">
                                                    <v-list-tile-content v-text="data.item.header" class="blue"></v-list-tile-content>
                                                </template>

                                                <!-- Normal item -->
                                                <template v-else>
                                                    <v-list-tile-content>
                                                    <v-list-tile-title v-html="data.item.text" class="ml-3"></v-list-tile-title>
                                                    <!-- <v-list-tile-sub-title v-html="data.item.group"/> -->
                                                    </v-list-tile-content>
                                                </template>
                                            </template>

                                            <template slot="selection" slot-scope="data">
                                                <v-chip close @input="removeUser(data.index)">{{ data.item.text }}</v-chip>
                                            </template>
                                        </v-select>
                                    </v-flex>

                                    <v-flex xs12>
                                        <v-select
                                            :items="levels"
                                            item-value="level_id"
                                            item-text="level_name"
                                            label="Penerima (Customer)"
                                            return-object
                                            multiple
                                            v-model="selected_levels"
                                        >
                                            <template
                                                slot="item"
                                                slot-scope="data">
                                                <v-list-tile-content>
                                                <v-list-tile-title v-html="data.item.level_name"></v-list-tile-title>
                                                <!-- <v-list-tile-sub-title v-html="data.item.group"/> -->
                                                </v-list-tile-content>
                                            </template>

                                            <template slot="selection" slot-scope="data">
                                                <v-chip close @input="removeLevel(data.index)">{{ data.item.level_name }}</v-chip>
                                            </template>
                                        </v-select>
                                    </v-flex>

                                    <v-flex xs12>
                                        <h5 class="caption">Sudah dibaca oleh :</h5>
                                        <v-chip v-for="(wh, n) in who_read" class="cyan lighten-4">{{ wh.user_full_name }}</v-chip>
                                        <v-chip v-show="customer_read_cnt>0" class="orange lighten-4">{{ customer_read_cnt }} customer</v-chip>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            
                        </v-layout>
                    </v-flex>
                    
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="save">Simpan</v-btn>                
            </v-card-actions>
        </v-card>
        <gpt></gpt>
    </v-dialog>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}

.watzap-text-container {
    /* background-image:url(../../assets/img/bg-whatsapp.png); */
    font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", 
  Helvetica, Arial, "Lucida Grande", sans-serif;
}

.watzap-text-container textarea {
    overflow: auto;
}

.watzap-text-container textarea::-webkit-scrollbar {
    width: .3em;
}

/* .watzap-text-container textarea::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
}

.watzap-text-container textarea::-webkit-scrollbar-thumb {
  background-color: darkgrey;
  outline: 1px solid slategrey;
} */
</style>
<script>
module.exports = {
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue'),
        "gpt" : httpVueLoader("./crm-info-gpt.vue")
    },

    data () {
        return {
            shortcodes: this.$store.state.info.shortcodes,
            imageName: '',
            imageUrl: '',
            imageFile: ''
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.info_new.dialog_new },
            set (v) { this.$store.commit('info_new/set_common', ['dialog_new', v]) }
        },

        info_title : {
            get () { return this.$store.state.info_new.info_title },
            set (v) { this.$store.commit('info_new/set_common', ['info_title', v]) }
        },

        info_content : {
            get () { return this.$store.state.info_new.info_content },
            set (v) { this.$store.commit('info_new/set_content', v) }
        },

        colors () {
            return this.$store.state.info_new.colors
        },

        selected_color : {
            get () { return this.$store.state.info_new.selected_color },
            set (v) { this.$store.commit('info_new/set_object', ['selected_color', v]) }
        },

        info_img : {
            get () {
                if (this.$store.state.info_new.info_img)
                    return this.$store.state.info_new.info_img
                return ''
            },
            set (v) { this.$store.commit('info_new/set_common', ['info_img', v]) }
        },

        edate : {
            get () { return this.$store.state.info_new.info_edate },
            set (v) { this.$store.commit('info_new/set_common', ['info_edate', v]) }
        },

        sdate : {
            get () { return this.$store.state.info_new.info_sdate },
            set (v) { this.$store.commit('info_new/set_common', ['info_sdate', v]) }
        },

        users () {
            return this.$store.state.info_new.users
        },

        selected_users : {
            get () { return this.$store.state.info_new.selected_users },
            set (v) { this.$store.commit('info_new/set_object', ['selected_users', v]) }
        },

        levels () {
            return this.$store.state.info_new.customer_levels
        },

        selected_levels : {
            get () { return this.$store.state.info_new.selected_customer_levels },
            set (v) { this.$store.commit('info_new/set_object', ['selected_customer_levels', v]) }
        },

        who_read () {
            return this.$store.state.info_new.who_read
        },

        customer_read_cnt () {
            return this.$store.state.info_new.customer_read_cnt
        }
    },

    methods : {
        save () {
            this.$store.dispatch('info_new/save')
        },

        insertAtCursor (x) {
            let t = this.$el.querySelector('.v-input__control')
            console.log(t)
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
                vue.info_img = photo_uri
                reader.onerror = function (error) {
                    console.log('Error: ', error);
                };
            }

            
        },

        clearImage() {
            this.imageUrl = ''
            this.imageName = ''
            this.imageFile = ''
            this.info_img = null
        },

        change_edate(x) {
            this.edate = x.new_date
        },

        change_sdate(x) {
            this.sdate = x.new_date
        },

        removeUser(idx) {
            let users = this.selected_users
            users.splice(idx, 1)
            this.selected_users = users
        },

        removeLevel(idx) {
            let levels = this.selected_levels
            levels.splice(idx, 1)
            this.selected_levels = levels
        },

        gpt () {
            this.$store.commit("info_new/set_object", ["dialog_gpt", true])
        }
    },

    mounted () {
        this.$store.dispatch('info_new/search_color')
        this.$store.dispatch('info_new/search_user').then((x) => {
            this.$store.dispatch('info_new/search_customer_level')
        })
    },

    watch : {}
}
</script>