self.addEventListener (`push`, event => {
    const ntf = event.data.json();
    event.waitUntil (self.registration.showNotification (ntf.title, {
        // badge: `/favicon.png`,
        body: ntf.body,
        data: {
            clkUrl: ntf.url,
            clkUrl2: ntf.url2,
        },
        icon: `/favicon.png`,
        renotify: true,
        // sound: ,
        tag: `MingledNtf`,
        timestamp: ntf.timestamp,
    }));
});
self.addEventListener (`notificationclick`, event => {
    const loginUrl = new URL(event.notification.data.clkUrl, self.location.origin).href;
    const pageUrl = new URL(event.notification.data.clkUrl2, self.location.origin).href;
    event.waitUntil (clients.matchAll ({
        type: `window`,
        includeUncontrolled: true
    }).then (windowClients => {
        let windowClient = windowClients.find (wc => wc.url.includes (`Login`));
        if (windowClient) {
            if (windowClient.url.includes (`Orders`)) windowClient.focus();
            else clients.openWindow (loginUrl);
            return;
        }
        windowClient = windowClients.find (wc => wc.url.includes (`Admin`));
        if (windowClient) {
            if (windowClient.url.includes (`Orders`)) windowClient.focus();
            else clients.openWindow (pageUrl);
            return;
        }
        clients.openWindow (loginUrl);
    }));
});
