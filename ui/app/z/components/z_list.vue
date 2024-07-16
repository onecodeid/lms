<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs6>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Daftar Gallery</h3>
                </v-flex>
                <v-flex xs3>
                    <v-select
                        :items="types"
                        v-model="selected_type"
                        label="Tipe"
                        return-object
                        item-value="id"
                        item-text="label"
                        solo
                        hide-details
                        clearable
                    ></v-select>
                </v-flex>
                <v-flex xs3 class="text-xs-right">
                    <!-- <v-btn color="success" class="ma-0 btn-icon" @click="add">
                        <v-icon>add</v-icon>
                    </v-btn> -->

                    <v-text-field
                        solo
                        hide-details
                        placeholder="Pencarian" v-model="query"
                        @change="search"
                    >
                        <template v-slot:append-outer>
                            <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                <v-icon>search</v-icon>
                            </v-btn>      

                            <v-btn color="success" class="ma-0 ml-2 btn-icon" @click="add">
                                <v-icon>add</v-icon>
                            </v-btn>  
                        </template>
                    </v-text-field>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-card v-for="(g, n) in galleries" :key="n" flat @click="select(g)">
                <v-card-text flat class="pa-1">
                    <v-layout row wrap>
                        <v-flex xs1>
                            <v-img :src="g.gallery_tmb" aspect-ratio="1.67"></v-img>        
                        </v-flex>
                        <v-flex xs10 class="pl-3">
                            <h3 class="title">{{g.gallery_title}}</h3>
                            <div class="body-1">{{type_name(g.gallery_type)}}</div>
                            <div class="caption">{{one_money(g.gallery_views)}} views
                                <span class="pl-2" v-show="g.gallery_type.indexOf('YOUTUBE') < 0">
                                    <span
                                        v-show="g.gallery_size < 10000">{{one_money(g.gallery_size)}} b</span>
                                    <span 
                                        v-show="g.gallery_size >= 10000 && g.gallery_size < 1000000">{{one_money(g.gallery_size/1000)}} kb</span>
                                </span>
                                <span v-show="g.gallery_type.indexOf('YOUTUBE') > -1 || g.gallery_type.indexOf('VIDEO') > -1" class="pl-2">
                                    {{g.gallery_duration}}
                                </span>
                            </div>
                        </v-flex>
                    </v-layout>
                    
                </v-card-text>
            </v-card>
            
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
                @input="change_page"
            ></v-pagination>
        </v-card-text>
        
        <common-dialog-delete :data="category_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
    </v-card>
</template>

<style scoped>
.v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}
</style>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "TIPE",
                    align: "left",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "JUDUL",
                    align: "left",
                    sortable: false,
                    width: "83%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        galleries () {
            return this.$store.state.z.galleries
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        category_id () {
            return this.$store.state.z.selected_gallery.gallery_id
        },

        query : {
            get () { return this.$store.state.z.search },
            set (v) { this.$store.commit('z/set_common', ['search', v]) }
        },

        curr_page : {
            get () { return this.$store.state.z.current_page },
            set (v) { this.$store.commit('z/set_common', ['current_page', v]) }
        },

        xtotal_page () {
            return this.$store.state.z.total_page
        },

        types () {
            return this.$store.state.z.types
        },

        selected_type : {
            get () { return this.$store.state.z.selected_search_type },
            set (v) { this.$store.commit('z/set_selected_search_type', v) }
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        add () {
            this.$store.commit('z/set_common', ['image_title', ''])
            this.$store.commit('z/set_image_caption', '')
            this.$store.commit('z/set_common', ['image_url', ''])
            this.$store.commit('z/set_common', ['video_id', ''])
            this.$store.commit('z/set_common', ['id', 0])
            this.$store.commit('z/set_selected_type', null)

            this.$store.commit('z/set_common', ['dialog', true])
            this.$store.commit('z/set_common', ['edit', false])
        },

        edit (x) {
            // this.select(x)
            
            this.$store.commit('z/set_common', ['image_title', x.gallery_title])
            this.$store.commit('z/set_image_caption', x.gallery_desc)
            this.$store.commit('z/set_common', ['image_url', x.gallery_url])
            this.$store.commit('z/set_common', ['video_id', x.gallery_video_id])
            this.$store.commit('z/set_common', ['id', x.gallery_id])
            this.$store.commit('z/set_common', ['edit', true])

            let y
            for (let t of this.types)
                if (t.id == x.gallery_type)
                    this.$store.commit('z/set_selected_type', t)

            this.$store.commit('z/set_common', ['dialog', true])
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('z/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('z/set_selected_gallery', x)
            this.edit(x)
        },

        search () {
            return this.$store.dispatch('z/search')
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('z/search')
        },

        type_name (x) {
            for (let t of this.types)
                if (t.id == x)
                    return t.label
            return ''
        }
    },

    mounted () {
        this.$store.dispatch('z/search')
    }
}
</script>