PT
Projecto back-end - Ilhéu Atelier

Este projecto vem da continuação do projecto que iniciei em front-end, utilizando a framework AngularJS.

É uma web-app de um atelier de arquitectura, que visa mostrar os seus projectos e ser uma plataforma de contacto entre clientes e arquitectos.

Esta segunda parte do projecto desenvolve toda a parte de back-end: base de dados MySQL, connecção ao front office e back office com PHP.

O acesso ao back office faz-se através de http://localhost/secretAdmin
O administrador logado tem acesso ao back office durante duas horas, após este tempo, o token expira, e é redirecionado para a página principal da web-app.

O back office dispõe das seguintes funcionalidades:

    -> Estatísticas de toda a informação presente na base de dados:
        -> contagem de administradores
        -> contagem de clientes que contactaram através da plataforma
        -> contagem de mensagem enviadas através da plataforma para a empresa
        -> contagem do número total de projectos na base de dados
        -> contagem do número total de imagens com projectos, na base de dados
        -> listagem de todos os projectos presentes na base de dados
    
    -> Gestão de administradores:
        -> criação de um novo admin
        -> actualização de informação sobre o admin logado
        -> remoção da conta do admin logado, se não for o único registado na base de dados
    
    -> Gestão de campos de informação na web-app:
        -> listagem de todas as informações
        -> criação de nova informação
        -> actualização de informação presente
        -> remoção de informação presente da base de dados

    -> Gestão de mensagens enviadas por clientes:
        -> listagem de todas as mensagens enviadas por clientes, ordenadas por data
        -> opção de envio de email de resposta a cada mensagem com opção de enviar anexos
        -> remoção de mensagem
    
    -> Gestão de projectos:
        -> Listagem de todos os projectos presentes na base de dados, com e sem imagens associadas
        -> criação de um novo projecto. com opção de adicionar imagens ou não.
        -> actualização de informação de um projecto
        -> remoção de um projecto da base de dados
        -> remoção de imagens individualmente de cada projecto.

    -> Logout

No formulário de contacto, os dados que o utilizador enviar serão guardados na base de dados, juntamente com o captcha, e que fará parte da validação da informação enviada para a base de dados.


EN
Full-stack project - Ilhéu Atelier

Ilhéu atelier is a web-app to showcase the projects of this architecture atelier, and to be a platform for clients to interact with the project oweners.

It implements AngularJs in the front-end, MySQL as DB and PHP to connect front-end and back-end.
Tha back office is accessible through http://localhost/secretAdmin

A logged in Admin have access to the back office for 2 hours, after that the token expires, and he will be redirected to the home page of the web-app.

Functionalities available in back office:
    -> Web-app statistics:
        -> number of administrators registered in DB
        -> number of clients that contacted through the platform
        -> number of mesages sent through the platform to the the company
        -> number of projects in the DB
        -> number of images associated to projects in the DB
        -> list of all projects in DB , with or without associated images
    
    -> Admin management:
        -> registration of new admins
        -> update information about the loged in admin
        -> deletion of loged in admin, if it's not the only one registered in DB
    
    -> Different information management:
        -> list of all informations
        -> creation of new information
        -> update information present in DB
        -> deletion of information from DB

    -> Messages sent from clients management:
        -> list of all sent messages, ordered by date
        -> option to reply to those messages by sending email
        -> deletion of messages from DB
    
    -> Projects management:
        -> list of all projects in DB , with or without associated images
        -> creation of new project. option to add images or not
        -> update info of a project
        -> deletion of a project from DB
        -> deletion of individual images from each project

    -> Logout

In the contact form, the information sent by the user will be stored in DB, as well as the generated captcha, and both data will be used to validate the information sent to the DB.
