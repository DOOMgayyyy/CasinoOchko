import React, { useState } from 'react';
import './Lobby.css';
import SideMenu from './SideMenu/SideMenu';
import PrivateRoom from './PrivateRoom/PrivateRoom';
import { IBasePage, PAGES } from '../PageManager';
 // Импортируем новый компонент

export interface LobbyProps extends IBasePage {
    player: {
        name: string;
        balance: number;
    };
    onQuickGame: () => void;
    onPrivateRoom: () => void;
    onShowLeaderboard: () => void;
    onShowChat: () => void;
    onShowRules: () => void;
    onShowAuthors: () => void;
    onLogout: () => void;
}

const Lobby: React.FC<LobbyProps> = ({ 
    player, 
    onQuickGame, 
    onShowLeaderboard, 
    onPrivateRoom,
    onShowChat,
    onShowRules,
    onShowAuthors,
    onLogout,
    
}) => {
    const [showSideMenu, setShowSideMenu] = useState(false);
    const [showPrivateRoomPage, setShowPrivateRoomPage] = useState(false);

    const [playerStats] = useState({
        totalGames: 156,
        totalWins: 89,
        totalMoney: 25400,
        totalHours: 47
    });

    const handleEditName = () => {
        console.log('Edit name clicked');
    };

    const handleCreateRoom = () => {
        console.log('Create private room');
        // Логика создания комнаты
    };

    const handleJoinRoom = () => {
        console.log('Join private room');
        // Логика присоединения к комнате
    };

    // Если показываем страницу приватной комнаты
    if (showPrivateRoomPage) {
        return (
            <PrivateRoom 
                player={player}
                onBack={() => setShowPrivateRoomPage(false)} // Возврат в лобби
                onCreateRoom={handleCreateRoom}
                onJoinRoom={handleJoinRoom}
                onShowSideMenu={() => setShowSideMenu(true)}
            />
        );
    }

 
    
    
    return (
        <div className="lobby">
            <div className="lobby-background"></div>
            
            <header className="lobby-header">
                <div className="header-left">
                    <button className="menu-btn" onClick={() => setShowSideMenu(true)}>☰</button>
                    <span className="player-name">{player.name}</span>
                </div>
            
                <div className="header-right">
                    <span className="balance-text">Ваш баланс: </span>
                    <span className="balance-amount">${player.balance}</span>
                    <button className="add-money-btn">+</button>
                </div>
            </header>

            <main className="lobby-main">
                <div className="logo">
                    <span className="logo-casino">CASIN</span>
                    <span className="logo-ochko">OCHKO</span>
                </div>
               
                <button className="lobby-btn quick-game-btn" onClick={onQuickGame}>
                    Быстрая игра
                </button>
                
                 <button className="lobby-btn private-room-btn" onClick={() => setShowPrivateRoomPage(true)}>
                    Приватная комната
                </button>
                
                <button className="lobby-btn leaderboard-btn" onClick={onShowLeaderboard}>
                    Таблица лидеров
                </button>
            </main>

            {showSideMenu && (
                <SideMenu 
                    player={player}
                    stats={playerStats}
                    onClose={() => setShowSideMenu(false)}
                    onEditName={handleEditName}
                    onShowRules={onShowRules}
                    onShowAuthors={onShowAuthors}
                    onLogout={onLogout}
                />
            )}

            
        </div>
    );
};

export default Lobby;