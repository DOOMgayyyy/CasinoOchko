import React, { useState } from 'react';
import Login from './components/Login/Login';
import Register from './components/Register/Register';
import Lobby from './components/Lobby/Lobby';

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
       <Lobby 
          player={{ name: 'Player', balance: 1000 }}
          onQuickGame={() => {}} // Пустая функция
          onPrivateRoom={() => {}} // Пустая функция
          onShowLeaderboard={() => {}} // Пустая функция
          onShowChat={() => {}} // Пустая функция
          onShowRules={() => {}} // Пустая функция
          onShowAuthors={() => {}} // Пустая функция
          onLogout={() => {
            window.location.hash = 'Login';
          }}
        />
    </div>
  );
};

export default App;
