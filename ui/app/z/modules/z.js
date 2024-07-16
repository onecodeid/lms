// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./z_api.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        edit: false,

        types: [],
        selected_type: null,
        selected_search_type: null,

        preview_name:'',

        id:0,
        image:'',
        image_view:0,
        image_size:0,
        image_title:'',
        image_caption:'',
        image_duration:'',
        image_url:'',

        image_file:'',
        image_name:'',
        image_tmb:'',
        image_type:'',
        upload: false,

        video_id:'',
        yt: true,

        galleries: [],
        selected_gallery: null,
        current_page: 1,
        total_page: 0,

        dialog: false
    },
    mutations: {
        set_common (state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        },

        set_types (state, v) {
            state.types = v
        },

        set_selected_type (state, v) {
            state.selected_type = v
        },

        set_selected_search_type (state, v) {
            state.selected_search_type = v
        },

        set_image_file (state, v) {
            state.image_file = v
        },

        set_image_caption (state, v) {
            state.image_caption = v
        },

        set_galleries (state, v) {
            state.galleries = v
        },

        set_selected_gallery (state, v) {
            state.selected_gallery = v
        }
    },
    actions: {
        async save(context) {
            // context.commit("update_search_status", 1)

            try {
                let prm = {}
                prm.type = context.state.selected_type.id
                prm.title = context.state.image_title
                prm.caption = context.state.image_caption
                prm.url = context.state.image_url
                prm.video_id = context.state.video_id
                prm.tmb = context.state.image_tmb
                prm.duration =context.state.image_duration
                prm.views = context.state.image_view
                prm.size = context.state.image_size

                if (context.state.edit)
                    prm.id = context.state.id

                prm.token =  one_token()
                
                let resp = await api.save(prm)
                console.log(resp)

                if (resp.status != "OK") {
                    // context.commit("update_search_status", 3)
                    // context.commit("update_search_error_message", resp.message)
                } else {
                    // context.commit("update_search_status", 2)
                    // context.commit("update_search_error_message", "")
                    
                    // let pr = resp.data
                    // context.commit('set_common', ['user_fullname', pr.user_fullname])
                    // context.commit('set_common', ['user_fulladdress', pr.user_fulladdress])
                    // context.commit('set_common', ['user_phone', pr.user_phone])
                    // context.commit('set_common', ['user_email', pr.user_email])
                    // context.commit('set_common', ['user_postcode', pr.user_postcode])
                    // context.commit('set_common', ['user_level', pr.user_level])

                    // context.commit('set_user', pr)
                    // context.dispatch('search_province', {})
                    context.commit('set_common', ['dialog', false])
                    context.dispatch('search')
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async uf(context) {
            // context.commit("update_search_status", 1)
            context.commit("set_common", ['upload', true])
            try {
                let prm = new FormData()
                prm.append("token", one_token())
                prm.append("file", context.state.image_file)
                let resp = await api.uf(prm)
                context.commit("set_common", ['upload', false])
                console.log(resp)

                if (resp.status != "OK") {
                    context.commit("set_common", ['search_status', 3])
                    context.commit("set_common", ['search_error_message', resp.message])
                } else {
                //     context.commit("update_search_status", 2)
                //     context.commit("update_search_error_message", "")
                    
                //     let pr = resp.data
                    context.commit('set_common', ['preview_name', resp.data.file_name])
                    context.commit('set_common', ['image_url', resp.data.file_url])
                    context.commit('set_common', ['image_title', resp.data.tmp_name])

                    if (resp.data.file_type == "mp4") {
                        context.commit('set_common', ['image_tmb', resp.data.tmb])
                    }
                //     context.commit('set_common', ['user_fulladdress', pr.user_fulladdress])
                //     context.commit('set_common', ['user_phone', pr.user_phone])
                //     context.commit('set_common', ['user_email', pr.user_email])
                //     context.commit('set_common', ['user_postcode', pr.user_postcode])
                //     context.commit('set_common', ['user_level', pr.user_level])

                //     context.commit('set_user', pr)
                //     context.dispatch('search_province', {})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async yt_api(context) {
            // context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.vid_id = context.state.video_id

                let resp = await api.yt_api(prm)
                context.commit('set_common', ['image_title', resp.data.title])
                context.commit('set_common', ['image_duration', resp.data.duration])
                context.commit('set_common', ['image_view', resp.data.views])
                context.commit('set_common', ['image_tmb', resp.data.thumbnail])
                context.commit('set_image_caption', resp.data.description)

            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },
        
        async search(context) {
            // context.commit("update_search_status", 1)

            try {
                let prm = {}
                prm.token =  one_token()
                prm.page = context.state.current_page
                prm.type = context.state.selected_search_type ? context.state.selected_search_type.id : ''
                
                let resp = await api.search(prm)
                console.log(resp)

                if (resp.status != "OK") {
                    context.commit("set_common", ['search_status', 3])
                    context.commit("set_common", ['search_error_message', resp.message])
                } else {
                    context.commit("set_common", ['search_status', 2])
                    context.commit("set_common", ['search_error_message', ""])
                    
                    let pr = resp.data
                    context.commit("set_galleries", pr.records)
                    context.commit("set_common", ['total_page', pr.total_page])
                    // context.commit('set_common', ['user_fullname', pr.user_fullname])
                    // context.commit('set_common', ['user_fulladdress', pr.user_fulladdress])
                    // context.commit('set_common', ['user_phone', pr.user_phone])
                    // context.commit('set_common', ['user_email', pr.user_email])
                    // context.commit('set_common', ['user_postcode', pr.user_postcode])
                    // context.commit('set_common', ['user_level', pr.user_level])

                    // context.commit('set_user', pr)
                    // context.dispatch('search_province', {})
                }
            } catch (e) {
                context.commit("set_common", ["search_status", 3])
                context.commit("set_common", ["search_error_message", e.message])
                console.log(e)
            }
        }
    }
}