import './Register.css';
import React, { useState } from 'react';

const Register: React.FC = () => {
  const [showPwd, setShowPwd] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);

const handleRegister = (e: React.FormEvent) => {
    e.preventDefault();

    window.location.hash = 'Lobby';
  };


  return (
    <div className="main-register">
      <main className="sign-wrap">
        {/* ЛОГОТИП */}
        <div className="brand-logo" aria-label="CASINOCHKO">
          <span className="brand-white">CASIN</span>
          <span className="brand-yellow">OCHKO</span>
        </div>

        <h1 className="sign-title">Регистрация</h1>

        <section className="sign-card" role="form" aria-label="Форма регистрации">
          <form onSubmit={handleRegister}>
          <div className="field">
            <label className="label" htmlFor="email">Email:</label>
            <input
              className="input"
              id="email"
              type="email"
              placeholder="your@example.com"
              autoComplete="username"
            />
          </div>

          <div className="field">
            <label className="label" htmlFor="password">Пароль:</label>
            <div className="password-wrap">
              <input
                className="input"
                id="password"
                type={showPwd ? 'text' : 'password'}
                placeholder="••••••••"
                autoComplete="new-password"
              />
              <button
                type="button"
                className="toggle-visibility"
                onClick={() => setShowPwd(v => !v)}
                aria-label={showPwd ? 'Скрыть пароль' : 'Показать пароль'}
                aria-pressed={showPwd}
                title={showPwd ? 'Скрыть пароль' : 'Показать пароль'}
              >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                     aria-hidden="true">
                  <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
          </div>

          <div className="field">
            <label className="label" htmlFor="passwordConfirm">Повторите пароль:</label>
            <div className="password-wrap">
              <input
                className="input"
                id="passwordConfirm"
                type={showConfirm ? 'text' : 'password'}
                placeholder="••••••••"
                autoComplete="new-password"
              />
              <button
                type="button"
                className="toggle-visibility"
                onClick={() => setShowConfirm(v => !v)}
                aria-label={showConfirm ? 'Скрыть пароль' : 'Показать пароль'}
                aria-pressed={showConfirm}
                title={showConfirm ? 'Скрыть пароль' : 'Показать пароль'}
              >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                     aria-hidden="true">
                  <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
          </div>

          <div className="field">
            <label className="label" htmlFor="username">Ваше имя:</label>
            <input className="input" id="username" type="text" autoComplete="nickname" placeholder="username" />
          </div>

          <div className="actions actions-split">
              {/* ИЗМЕНИТЬ ССЫЛКУ НА КНОПКУ С ОБРАБОТЧИКОМ */}
              <button 
                type="button"
                className="btn-link" 
                onClick={(e) => {
                  e.preventDefault();
                  window.location.hash = 'Login';
                }}
                >
              <span className="arrow">&lt;</span>
              <span>авторизация</span>
            </button>

            <button className="btn-link" type="submit">
              <span className="arrow">&gt;</span>
              <span>создать аккаунт</span>
            </button>
          </div>
          </form>
        </section>
      </main>
    </div>
  );
};

export default Register;
