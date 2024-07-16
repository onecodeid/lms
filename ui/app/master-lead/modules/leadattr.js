import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        searchLeadAttrCode: null,

        leadAttrs: [],
        total: 0,
        totalPage: 1,
        selectedLeadAttr: null,

        leadAttrCodes: [{id:'LEAD.PROBLEM',text:'PROBLEM KULIT'},{id:'LEAD.GREETING',text:'TIPE GREETING'},{id:'LEAD.PRECLOSE',text:'KENDALA CLOSING'}],
        selectedLeadAttrCode: null,

        attrName: '',
        attrCode: '',
        attrId: 0,

        dialog: false,
        edit: false,

        current_page: 1
    },
    mutations: {
        setObject(state, v) {
            let name = v[0]
            let val = v[1]
            state[name] = val
        }
    },
    actions: {
        async search({state, commit, dispatch}) {
            let prm = {
                token: one_token(),
                page: state.current_page,
                search: state.search
            }
            if (state.searchLeadAttrCode&&state.searchLeadAttrCode!='undefined')
                prm.code = state.searchLeadAttrCode

            return dispatch("system/postme", {
                url: "master/leadattr/search",
                prm: prm,
                callback: function (d) {
                    commit('setObject', ['leadAttrs', d.records])
                    commit('setObject', ['total', d.total])
                    commit('setObject', ['totalPage', d.total_page])
                    return d
                }
            }, { root: true })

        },

        async save({state, commit, dispatch}) {
            let prm = {
                token: one_token(),
                attr_name: state.attrName,
                attr_code: state.selectedLeadAttrCode
            }

            if (state.edit)
                prm.attr_id = state.selectedLeadAttr.attr_id

            return dispatch("system/postme", {
                url: "master/leadattr/save",
                prm: prm,
                callback: function (d) {
                    dispatch("search")
                    commit('setObject', ['dialog', false])
                    return d
                }
            }, { root: true })
        },

        async del({state, commit, dispatch}) {
            let prm = {
                token: one_token(),
                id: state.selectedLeadAttr.attr_id
            }

            return dispatch("system/postme", {
                url: "master/leadattr/del",
                prm: prm,
                callback: function (d) {
                    dispatch("search")
                    commit('setObject', ['dialog', false])
                    return d
                }
            }, { root: true })
        }
    }
}