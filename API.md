Описание API
Здесь описано всё АПИ, используемое в приложении, с описанием структур данных
Содержание

Общее
1.1. Адрес сервера
1.2. Используемый протокол


Структуры данных
2.1. Общий формат ответа
2.2. Пользователь
2.3. Сообщение


Список запросов
3.1. Общие ошибки


Подробно
4.1. login
4.2. logout
4.3. registration
4.4. updateUserName
4.5. sendMessage
4.6. getMessages



1. Общее
1.1. Адрес сервера
http://nopainnogame.local/api
1.2. Используемый протокол
API полностью реализовано на http(s). 
Формат возвращаемых значений JSON.
Все методы, если это особо не оговорено, имеют тип GET. Метод updateUserName использует POST.
2. Структуры данных
2.1. Общий формат ответа
T - какие-то данные. В случае успешного ответа возвращается result = 'ok' и поле data с данными.
В случае ошибки возвращается result = 'error' и поле error с кодом и текстом ошибки
Answer<T>: {
    result: 'ok' | 'error';
    data?: T;
    error?: {
        code: number;
        text: string;
    };
}

2.2. Пользователь
User: {
    id: number;
    email: string;
    token: string;
    name?: string;
}

2.3. Сообщение
Message: {
    message: string;
    author: string;
    created: string;
}

3. Список запросов



Название
О чем



login
Авторизация пользователя


logout
Логаут пользователя


registration
Регистрация пользователя


updateUserName
Обновление имени пользователя


sendMessage
Отправить сообщение в чат


getMessages
Получить сообщения в чате


3.1. Общие ошибки

101 - Param method not setted
102 - Method not found
242 - Params not set fully
705 - User is not found
9000 - unknown error

4. Подробно
4.1. login
Авторизация пользователя в системе
Параметры
{
    email: string; - email пользователя
    hash: string; - контрольная сумма, равная hash = md5(password + rnd)
    rnd: number; - случайное целое число
}

Успешный ответ
    Answer<User>

Ошибки

242 - Params not set fully
1002 - Wrong login or password
1005 - User is no exists

4.2. logout
Выход пользователя из системы
Параметры
{
    token: string; - токен авторизации
}

Успешный ответ
    Answer<true>

Ошибки

`VARCHAR

System: 242 - Params not set fully

705 - User is not found
1003 - Error to logout user

4.3. registration
Регистрация нового пользователя
Параметры
{
    email: string; - email пользователя (должен быть валидным, например, user@example.com)
    password: string; - пароль пользователя
    name: string; - имя пользователя
}

Примечание: Email должен быть валидным (проверяется сервером с помощью фильтра валидации email). Если email не соответствует формату, возвращается ошибка 242.
Успешный ответ
    Answer<User>

Ошибки

242 - Params not set fully или email невалиден
1001 - Is it unique login?
1007 - user with this email is already registered
1004 - Error to register user

4.4. updateUserName
Обновление имени пользователя
Тип запроса: POST
Параметры JSON body
{
    method: "updateUserName";
    token: string; - токен авторизации
    newName: string; - новое имя пользователя
}

Успешный ответ
    Answer<User>

Ошибки

242 - Params not set fully
705 - User is not found
1009 - Error updating user name
1010 - Name is already taken

4.5. sendMessage
Отправка сообщения в чат
Параметры
{
    token: string; - токен авторизации
    message: string; - текст сообщения
}

Успешный ответ
    Answer<true>

Ошибки

242 - Params not set fully
705 - User is not found
706 - text message is empty
707 - could not send message

4.6. getMessages
Получение всех сообщений чата
Параметры
{
    token: string; - токен авторизации
    hash: string; - хеш-сумма чата
}

Успешный ответ
    Answer<{
        messages: Message[]; - список сообщений
        hash: string; - новый хеш чата
    }>

Примечание: в случае отсутствия новых сообщений будет ответ:
    Answer<{
        hash: string; - новый хеш чата
    }>

Ошибки

242 - Params not set fully
705 - User is not found
