import './RegisterPage.css';
import React from 'react';

const RegisterPage: React.FC = () => {
  return (
    <div className="main-register-page">
      <main className="sign-wrap">
        <h1 className="sign-title">Регистрация</h1>

        <section className="sign-card" role="form" aria-label="Форма входа">
          <div className="field">
            <label className="label" htmlFor="email">Email:</label>
            <input className="input" id="email" type="email" placeholder="you@example.com" autoComplete="username" />
          </div>

          <div className="field">
            <label className="label" htmlFor="password">Пароль:</label>
            <input className="input" id="password" type="password" placeholder="••••••••" autoComplete="current-password" />
          </div>

          <div className="field">
            <label className="label" htmlFor="username">Отображаемое имя:</label>
            <input className="input" id="username" type="text" autoComplete="username" placeholder="username" />
          </div>

          <div className="actions">
            <button className="btn-link" type="button">
              <span className="arrow">&gt;</span>
              <span>создать аккаунт</span>
            </button>
          </div>
        </section>
      </main>
    </div>
  );
};

export default RegisterPage;
