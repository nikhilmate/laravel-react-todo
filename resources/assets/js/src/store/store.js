function CreateInitState() {
    return Object.assign(
        {},
        {
            e_data: {
                auther: {
                    recon_text: null,
                    recon_eid: null,
                    recon_secret: null
                },
                passport: null,
                data_array: []
            },
            appData: {
                app_header: null,
                app_body: null
            }
        }
    );
}

// actions
const CREATE_USER = "CREATE_USER";

function mainReducer(state = CreateInitState(), action) {
    switch (action.type) {
        case "GET_DATA":
            return state;
        default:
            return state;
    }
}
