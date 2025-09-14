import './SignInPage.css';
import React from 'react';

type Props = {
  showPage: (page: string) => void; // <-- простой колбэк
};

const SignInPage: React.FC<Props> = ({ showPage }) => {
  return (
    <div className="main-sign-in">
      <main className="sign-wrap">
        <h1 className="sign-title">Добро Пожаловать!</h1>

        <section className="sign-card" role="form" aria-label="Форма входа">
          <div className="field">
            <label className="label" htmlFor="email">Email:</label>
            <input className="input" id="email" type="email" placeholder="you@example.com" autoComplete="username" />
          </div>

          <div className="field">
            <label className="label" htmlFor="password">Пароль:</label>
            <input className="input" id="password" type="password" placeholder="••••••••" autoComplete="current-password" />
          </div>

          <div className="actions">
            <button className="btn-link" type="button">
              <span className="arrow">&gt;</span>
              <span>войти</span>
            </button>
          </div>
        </section>

        <div className="foot">
          Впервые здесь?
          <a
            className="foot-link"
            href="#"
            onClick={(e) => {
              e.preventDefault();
              showPage('RegisterPage');
            }}
          >
            <span className="arrow">&gt;</span>
            <span>зарегистрироваться</span>
          </a>
        </div>
      </main>
    </div>
  );
};

export default SignInPage;
