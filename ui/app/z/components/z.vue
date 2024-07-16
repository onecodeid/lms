<template>
    <v-dialog
        v-model="dialog"
        scrollable 
        persistent :overlay="false"
        max-width="800px"
        transition="dialog-transition"
        v-if="dialog"
    >
        <v-card>
            <v-card-title class="zalfa-bg-purple py-2 white--text">
                <h3 class="headline">GALLERI BARU</h3>
            </v-card-title>
            <v-card-text class="pa-2">
                <v-layout row wrap>
                    <v-flex md6 pr-2>
                        <v-select
                            :items="types"
                            v-model="selected_type"
                            label="Mau Upload Apa / Kemana ?"
                            outline
                            return-object
                            item-value="id"
                            item-text="label"
                            :disabled="edit"
                        ></v-select>

                        <v-text-field
                            label="Youtube Video ID"
                            placeholder="5PJ3quyP5ls"
                            v-show="selected_type&&
                                    ['P-YOUTUBE','Z-VIDEO'].indexOf(selected_type.id)>-1"
                            v-model="video_id"
                            @change="reload_yt"
                            :disabled="edit"
                        >
                            <template slot="prepend-inner">
                                <div class="pt-1 body-1 grey--text">https://youtube.com/watch?v=</div>
                            </template>

                            <template slot="append-outer">
                                <v-btn color="red btn-icon ma-0" dark small>
                                    <v-icon medium>play_arrow</v-icon>
                                </v-btn>
                            </template>
                        </v-text-field>

                        <v-text-field label="Select File" 
                            @click='pickFile' 
                            prepend-icon='attach_file'
                            v-show="selected_type&&
                                    ['P-YOUTUBE','Z-VIDEO'].indexOf(selected_type.id)<0"
                            :hide-details="!!upload"
                            :disabled="!!upload"
                            v-model="image_name">
                        </v-text-field>

                        <input
                            type="file"
                            style="display: none"
                            ref="image"
                            :accept="selected_type&&selected_type.type=='image'?'image/*':'video/mp4'"
                            @change="onFilePicked"
                        >

                        <div class="px-2" v-show="selected_type&&upload"><v-progress-linear :indeterminate="true"></v-progress-linear></div>
                        
                        <!-- <v-text-field
                            label="Upload File"
                            type="file"
                        ></v-text-field> -->

                        <v-text-field
                            :label="'Judul '+title"
                            v-model="image_title"
                        ></v-text-field>

                        <v-textarea
                            :label="'Caption '+title"
                            v-model="image_caption"></v-textarea>

                        
                    </v-flex>

                    <v-flex xs6 pl-2>
                        <zp></zp>
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-btn color="success" @click="dialog=!dialog" flat>Tutup</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="success" @click="save">SIMPAN</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports = {
    components : {
        "zp" : httpVueLoader("./z_preview.vue"),
    },
    computed : {
        selected_type : {
            get () { return this.$store.state.z.selected_type },
            set (v) { this.$store.commit('z/set_selected_type', v) }
        },

        types () {
            return this.$store.state.z.types
        },

        title () {
            if (!this.selected_type) return ''
            switch (this.selected_type.type) {
                case 'image':
                    return 'Gambar';
                case 'video':
                    return 'Video';
                default:
                    return 'Dokumen';
            }
        },

        image_name : {
            get () { return this.$store.state.z.image_name },
            set (v) { this.$store.commit('z/set_common', ['image_name', v]) }
        },

        image_size : {
            get () { return this.$store.state.z.image_size },
            set (v) { this.$store.commit('z/set_common', ['image_size', v]) }
        },

        image_type : {
            get () { return this.$store.state.z.image_type },
            set (v) { this.$store.commit('z/set_common', ['image_type', v]) }
        },

        image_url : {
            get () { return this.$store.state.z.image_url },
            set (v) { this.$store.commit('z/set_common', ['image_url', v]) }
        },

        image_title : {
            get () { return this.$store.state.z.image_title },
            set (v) { this.$store.commit('z/set_common', ['image_title', v]) }
        },

        image_caption : {
            get () { return this.$store.state.z.image_caption },
            set (v) { this.$store.commit('z/set_image_caption', v) }
        },

        image_file : {
            get () { return this.$store.state.z.image_file },
            set (v) { this.$store.commit('z/set_image_file', v) }
        },

        upload () { return this.$store.state.z.upload },

        video_id : {
            get () { return this.$store.state.z.video_id },
            set (v) { this.$store.commit('z/set_common', ['video_id', v]) }
        },

        dialog : {
            get () { return this.$store.state.z.dialog },
            set (v) { this.$store.commit('z/set_common', ['dialog', v]) }
        },

        edit () { return this.$store.state.z.edit }
    },

    methods : {
        pickFile () {
            this.$refs.image.click ()
        },

        onFilePicked (e) {
            
			const files = e.target.files
			if(files[0] !== undefined) {
                this.image_name = files[0].name
                this.image_size = files[0].size
                this.image_type = files[0].type
                
				if(this.image_name.lastIndexOf('.') <= 0) {
					return
				}
				const fr = new FileReader ()
                fr.readAsDataURL(files[0])
				fr.addEventListener('load', () => {
                    // this.imageUrl = fr.result
                    this.image_file = files[0] // this is an image file that can be sent to server...
                    console.log(this.image_file)
                    this.$store.dispatch('z/uf')
                    // this.getBase64(files[0])
				})
			} else {
				this.image_name = ''
				this.image_file = ''
				this.image_url = ''
			}
        },

        reload_yt () {
            var thiss = this.$store
            thiss.commit('z/set_common', ['yt', false])
            thiss.dispatch('z/yt_api')
            setTimeout(function() {
                thiss.commit('z/set_common', ['yt', true])
            }, 200)
        },

        save () {
            this.$store.dispatch('z/save')
        }
    },

    mounted () {
        this.$store.commit('z/set_types', [
            {id:'P-IMAGE', type:'image', label: 'PROMOSI PRODUK :: GAMBAR'},
            {id:'P-VIDEO', type:'video', label: 'PROMOSI PRODUK :: VIDEO FILE'},
            {id:'P-YOUTUBE', type:'video', label: 'PROMOSI PRODUK :: VIDEO YOUTUBE'},
            {id:'Z-YOUTUBE', type:'video', label: 'PEMBELAJARAN :: VIDEO YOUTUBE'},
            {id:'Z-DOC-PDF', type:'doc', label: 'PEMBELAJARAN :: DOCUMENT PDF'}
        ])
        
    }
}
</script>
