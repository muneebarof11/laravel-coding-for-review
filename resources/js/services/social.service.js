import {HttpService} from "./base.service";

export class SocialService extends HttpService {
    prefix = 'social';

    stats = () => this.get(`${this.prefix}/stats`, {});

    suggestions = () => this.get(`${this.prefix}/connections/suggestion`, {});

    sentRequests = () => this.get(`${this.prefix}/requests/sent`, {});

    receivedRequests = () => this.get(`${this.prefix}/requests/received`, {});

    connections = () => this.get(`${this.prefix}/connections`, {});

    acceptRequest = (id) => this.put(`${this.prefix}/requests/sent/${id}`, {});

    connectWithUser = (body) => this.post(`${this.prefix}/requests/sent`, body);

    removeConnection = (id) => this.delete(`${this.prefix}/connections/${id}`, {})

    withdrawRequest = (id) => this.delete(`${this.prefix}/requests/sent/${id}`, {})
}

export default new SocialService();
