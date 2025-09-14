import React, { useState } from 'react';
import SignInPage from './js/components/SignInPage/SignInPage';
import RegisterPage from './js/components/RegisterPage/RegisterPage';

let setPageExternal: ((p: string) => void) | null = null;
window.addEventListener('hashchange', () => {
  const p = window.location.hash.replace('#', '') || 'SignInPage';
  setPageExternal?.(p);
});

const App = () => {
  const [page, setPage] = useState(
    window.location.hash.replace('#', '') || 'SignInPage'
  );
  setPageExternal = setPage;

  const showPage = (newPage: string) => {
    window.location.hash = newPage;
  };

  return (
    <div>
      {page === 'SignInPage' && <SignInPage showPage={showPage} />}
      {page === 'RegisterPage' && <RegisterPage />}
    </div>
  );
};

export default App;
