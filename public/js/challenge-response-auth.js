'use strict';

document.addEventListener('DOMContentLoaded', function (event) {

    let element = document.querySelector('#loginform');

    element.addEventListener('submit', event => {

        event.preventDefault();

        let password = document.getElementById('user_pass');
        let username = document.getElementById('user_login').value;

        fetchUserSalt(username).then(obj => {
            let hash = bcrypt.hashSync(password.value, obj.salt); // equal with DB Hash
            password.value = bcrypt.hashSync(hash + obj.challenge); // hash + server side challenge
            document.getElementById('loginform').submit();

        }).catch((e) => {
            document.getElementById('loginform').submit();
        });
    });
});

function fetchUserSalt(username) {
    return fetch('wp-json/mb-challenge/get-user-salt-and-challenge/' + username).then(response => response.json());
}