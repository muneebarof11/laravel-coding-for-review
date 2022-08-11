import React, {useEffect, useState, StrictMode, Suspense } from 'react';
import { createRoot } from "react-dom/client";
import {Suggestions} from "./Suggestions";
import {SentRequests} from "./SentRequests";
import {ReceivedRequests} from "./ReceivedRequests";
import {Connections} from "./Connections";
import socialService from "../services/social.service";
import {Loader} from "./Loader";

export const App = () => {
    const [component, setComponent] = useState('suggestions');
    const [stats, setStats] = useState({
        connections: 0,
        sent: 0,
        received: 0,
        suggestion: 0
    })

    const changeActiveComponent = (e) => {
        setComponent(e.target.value);
    }

    const getStats = () => {
        socialService.stats().then(response => {
            setStats({...response.data.data});
        });
    }

    const TABS_COMPONENTS = {
        suggestions: {type: Suggestions, params: {updateStats: getStats}},
        sentRequests: {type: SentRequests, params: {updateStats: getStats}},
        receivedRequests: {type: ReceivedRequests, params: {updateStats: getStats}},
        connections: {type: Connections, params: {updateStats: getStats}},
    };

    useEffect(() => {
        getStats();
    }, [])

    let ActiveComponent = TABS_COMPONENTS[component].type;
    return (
        <>
            <div className="card-body">
                <div className="btn-group w-100 mb-3" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" checked={component === 'suggestions'} value="suggestions" className="btn-check" name="btnradio" id="Suggestions" onChange={changeActiveComponent}  />
                    <label className="btn btn-outline-primary" htmlFor="Suggestions" id="get_suggestions_btn">Suggestions ({stats.suggestion})</label>

                    <input type="radio" checked={component === 'sentRequests'} value="sentRequests" className="btn-check" name="btnradio" id="SentRequests" onChange={changeActiveComponent}    />
                    <label className="btn btn-outline-primary" htmlFor="SentRequests" id="get_suggestions_btn">Sent Requests ({stats.sent})</label>

                    <input type="radio" checked={component === 'receivedRequests'} value="receivedRequests" className="btn-check" name="btnradio" id="ReceivedRequests" onChange={changeActiveComponent} />
                    <label className="btn btn-outline-primary" htmlFor="ReceivedRequests" id="get_suggestions_btn">Received Requests ({stats.received})</label>

                    <input type="radio" checked={component === 'connections'} value="connections" className="btn-check" name="btnradio" id="Connections" onChange={changeActiveComponent} />
                    <label className="btn btn-outline-primary" htmlFor="Connections" id="get_suggestions_btn">Connections ({stats.connections})</label>
                </div>
                <hr />
                {
                    <ActiveComponent {...TABS_COMPONENTS[component].params} />
                }
            </div>
        </>
    )
}

const rootElement = document.getElementById("content");
if(rootElement) {
    const root = createRoot(rootElement);
    root.render(
        <StrictMode>
            <Suspense fallback={<Loader/>}>
                <App/>
            </Suspense>
        </StrictMode>
    );
}
