import React, { useState } from 'react';
import Login from './components/Login/Login';
import Register from './components/Register/Register';

export const StoreContext = React.createContext<any>({ getUser: () => null });
// React.createContext<Store>(null!);
export const ServerContext = React.createContext<any>({});
// React.createContext<Server>(null!);

let setPageExternal: ((p: string) => void) | null = null;
window.addEventListener('hashchange', () => {
  const p = window.location.hash.replace('#', '') || 'Login';
  setPageExternal?.(p);
});

const App = () => {
  const [page, setPage] = useState(
    window.location.hash.replace('#', '') || 'Login'
  );
  setPageExternal = setPage;

  return (
    <div>
      {page === 'Login' && <Login />}
      {page === 'Register' && <Register />}
    </div>
  );
};

export default App;
