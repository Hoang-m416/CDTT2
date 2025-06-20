document.addEventListener('DOMContentLoaded', () => {
    const searchIcon = document.querySelector('#search-icon');
    const search = document.querySelector('.search-box');
    const navbar = document.querySelector('.navbar');
    const header = document.querySelector('.header');
    const registerLink = document.querySelector('.register-link');
    const loginLink = document.querySelector('.login-link');
    const wrapper = document.querySelector('.wrapper');
    const menuToggle = document.querySelector('.menu-toggle');
    const sideMenu = document.getElementById('sideMenu');
    const overlay = document.getElementById('menuOverlay');
    const userAuthButton = document.querySelector('.user-auth');
    const authLinks = document.querySelector('.auth-links');
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');

    if (searchIcon && search) {
        searchIcon.onclick = () => search.classList.toggle('active');
    }

    if (navbar) {
        document.querySelector('#menu-icon').addEventListener('click', () => {
            navbar.classList.toggle('active');
            search.classList.remove('active');
        });

        window.onscroll = () => {
            navbar.classList.remove('active');
            search.classList.remove('active');
        };
    }

    if (header) {
        window.addEventListener('scroll', () => {
            header.classList.toggle('shadow', window.scrollY > 0);
        });
    }

    if (wrapper) {
        wrapper.classList.add('active');
    }

    if (registerLink && loginLink && wrapper) {
        registerLink.onclick = () => wrapper.classList.add('active');
        loginLink.onclick = () => wrapper.classList.remove('active');
    }

    if (menuToggle && sideMenu && overlay) {
        menuToggle.addEventListener('click', (e) => {
            e.preventDefault();
            sideMenu.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sideMenu.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    if (wrapper) {
        if (tab === 'register') {
            wrapper.classList.add('active');
        } else {
            wrapper.classList.remove('active');
        }
    }

    userAuthButton.addEventListener('click', (e) => {
        e.stopPropagation();
        authLinks.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!userAuthButton.contains(e.target) && !authLinks.contains(e.target)) {
            authLinks.classList.remove('active');
        }
    });

    authLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            authLinks.classList.remove('active');
        });
    });
});