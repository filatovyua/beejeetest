const DEFAULT_NEW_STATUS = 0;
const REDACTED_STATUS = 2;
const COUNT_ROWS_PER_PAGE = 3;
const LANGUAGE = 'ru';
const PAGINATE_SETTINGS = {
    responsive: true,
    stateful: true,
    emptyCell: "",
    td: {
        class: "tasks__table-td"
    }
}
let DATA_TABLE = [];

const paginate = ($table, data) => {
    $table.smpSortableTable(data, COUNT_ROWS_PER_PAGE, LANGUAGE, PAGINATE_SETTINGS);
}

const htmlspecialchars = (str) => {
    var div = document.createElement('div');
    div.innerText = str;
    return div.innerHTML;
}
const validateEmail = (email) => {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}
const addNewTask = () => {
    let body = document.querySelector(".tasks__table");
    let rowNew = body.querySelector(".tasks__new");

    rowNew.querySelectorAll(".input--error").forEach((cur) => {
        cur.classList.remove("input--error");
    });
    document.querySelector('.error-message').innerHTML = '';

    let nameEl = rowNew.querySelector(".tasks__new-name input");
    let erEl = [];
    if (!nameEl.value || nameEl.value.length === 0) {
        erEl.push(nameEl);
    }
    let emailEl = rowNew.querySelector(".tasks__new-email input");
    if (!validateEmail(emailEl.value)) {
        erEl.push(emailEl);
    }
    let contentEl = rowNew.querySelector(".tasks__new-content input");
    if (!contentEl.value || contentEl.value.length === 0) {
        erEl.push(contentEl)
    }
    if (erEl.length > 0) {
        return erEl.map((cur) => {
            cur.classList.add('input--error');
        })
    }
    let params = {name: nameEl.value, email: emailEl.value, content: contentEl.value};
    fetch("task/add", {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(params)
    })
            .then(response => response.json())
            .then((data) => {

                if (data.error) {
                    return document.querySelector('.error-message').innerHTML = data.message || 'error';
                }
                window.location.reload();
            })
}

const auth = () => {
    window.location.href = "auth";
}

const logout = () => {
    fetch("auth/logout")
            .then(() => {
                window.location.reload();
            })
}
const preEdit = (el) => {
    let id = el.dataset.id;
    let key = el.dataset.key;
    let value = (key === 'status' ? el.dataset.status : el.value);
    el.dataset.old = value;
}
const edit = (el) => {

    let id = el.dataset.id;
    let key = el.dataset.key;
    let value = (key === 'status' ? el.dataset.status : el.value);

    if (key !== 'status' && el.dataset.old && el.dataset.old === value) {
        return;
    }

    if (!value || value.length === 0) {
        return el.classList.add('input--error');
    }
    if (key === 'email' && !validateEmail(value)) {
        return el.classList.add('input--error');
    }

    fetch("task/edit", {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({id: id, key: key, value: value})
    })
            .then((response) => response.json())
            .then((data) => {
                if (data.error) {
                    return document.querySelector('.error-message').innerHTML = data.message || 'error';
                }
                if (key === 'status') {
//                    window.location.reload();
                }
            })
}

document.addEventListener("DOMContentLoaded", () => {
    let isAuthed = document.querySelector('.tasks').dataset.is_authed;
    fetch("task/all")
            .then(response => response.json())
            .then((data) => {
                let formatted = (DATA_TABLE = data['data']).map((cur) => {
                    cur.but = '';
                    if (isAuthed) {

                        if ([DEFAULT_NEW_STATUS, REDACTED_STATUS].includes(cur.status))
                            cur.but = `<button type='button' class='btn btn-info buttons__button-success' data-id='${cur.id}' data-key='status' data-status='1'>Выполнена</button>`;

                        cur.name = {
                            text: `<input class="form-control tasks__table-input--editable" type='text' data-id='${cur.id}' data-key='name'  value='${cur.name}' />`,
                            sort: cur.name
                        };
                        cur.email = {
                            text: `<input class="form-control tasks__table-input--editable" type='text' data-id='${cur.id}' data-key='email' value='${cur.email}' />`,
                            sort: cur.email
                        };
                        cur.content = {
                            text: `<input class="form-control tasks__table-input--editable" type='text' data-id='${cur.id}' data-key='content' value='${cur.content}' />`,
                            sort: cur.content
                        };
                    }
                    cur.status = data['statuses'][cur.status] || '';
                    return cur;
                })
                paginate($(".tasks__table"), formatted);
            })
});

document.addEventListener('click', (event) => {
    let el = event.target;
    if (el.classList.contains('buttons__button-add')) {
        return addNewTask();
    }
    if (el.classList.contains('buttons__button-auth')) {
        return auth();
    }
    if (el.classList.contains('buttons__button-logout')) {
        return logout();
    }
    if (el.classList.contains('buttons__button-success')) {
        return edit(el);
    }
});

document.addEventListener('focusout', (event) => {
    let el = event.target;
    if (el.classList.contains('tasks__table-input--editable')) {
        return edit(el);
    }
})
document.addEventListener('focusin', (event) => {
    let el = event.target;
    if (el.classList.contains('tasks__table-input--editable')) {
        return preEdit(el);
    }
})

