// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import { one_token, insertAtCursor } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',

        info_id: 0,
        info_title: '',
        info_content: '',
        info_img: '',
        info_content: '',
        info_date: '',
        info_number: '',
        info_up: '',
        info_sdate: '',
        info_edate: '',

        dialog: false
    },
    mutations: {
        set_common(state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        },

        set_object(state, val) {
            state[val[0]] = val[1]
        }
    },
    actions: {
        async gets(context) {
            let prm = {
                token: one_token(),
                id: context.state.info_id
            }

            context.dispatch("system/postme", {
                url: "crm/info/get",
                prm: prm,
                callback: function(d) {
                    context.commit('set_object', ['info_content', "<p>" + d.info_content.replaceAll("\n", "</p><p>") + "</p>"])
                    context.commit('set_object', ['info_date', d.info_date])
                    context.commit('set_object', ['info_img', d.info_img])
                    context.commit('set_object', ['info_number', d.info_number])
                    context.commit('set_object', ['info_sdate', d.info_sdate])
                    context.commit('set_object', ['info_edate', d.info_edate])
                    context.commit('set_object', ['info_title', d.info_title])
                    context.commit('set_object', ['info_up', d.info_up])
                }
            }, { root: true })

        }
    }
}