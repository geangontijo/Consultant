const makeWASocket = require('@adiwajshing/baileys').default
const {DisconnectReason, useMultiFileAuthState, promiseTimeout} = require('@adiwajshing/baileys')
const {MongoClient} = require('mongodb')
const mongoClient = new MongoClient('mongodb://root:123456@localhost:27017')

async function connectToWhatsApp() {
    const {state, saveCreds} = await useMultiFileAuthState('auth_info_baileys')
    await mongoClient.connect()
    const connection = mongoClient.db('consultant')
    await connection.command({ping: 1})
    console.log("MongoDB Connected")

    const jobs = connection.collection('jobs')

    const sock = makeWASocket({
        // can provide additional config here
        printQRInTerminal: true,
        auth: state
    })
    sock.ev.on('connection.update', async (update) => {
        const {connection, lastDisconnect} = update
        if (connection === 'close') {
            const shouldReconnect = lastDisconnect.error?.output?.statusCode !== DisconnectReason.loggedOut
            console.log('connection closed due to ', lastDisconnect.error, ', reconnecting ', shouldReconnect)
            // reconnect if not logged out
            if (shouldReconnect) {
                await connectToWhatsApp()
            }
        } else if (connection === 'open') {
            console.log('opened connection')

            const filter = {
                queue: 'whatsapp',
            };

            setInterval(async () => {
                const updateResult = await jobs.updateMany(filter, {$set: {reserved_at: Math.floor(Date.now() / 1000)}});

                if (updateResult.modifiedCount === 0) {
                    return;
                }
                const messages = await jobs.find(filter).toArray();

                for (const message of messages) {
                    const payload = JSON.parse(message.payload);
                    await sock.sendMessage(`55${payload.data.to}@s.whatsapp.net`, {
                        text: payload.data.message
                    })

                    jobs.deleteOne({_id: message._id}).then(r => console.log(`message ${message._id} processed`))
                }
            }, 500)
            // sock.sendMessage('5537991524432@s.whatsapp.net', {
            //     text: 'Hello, world!'
            // })
        }
    })
    sock.ev.on('creds.update', saveCreds)
}

connectToWhatsApp();

mongoClient.close()
