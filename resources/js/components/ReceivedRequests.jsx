import React, {useEffect, useState, StrictMode } from 'react';
import socialService from "../services/social.service";
import {Loader} from "./Loader";

export const ReceivedRequests = (props) => {
    const [receivedRequests, setReceivedRequests] = useState({data: []});
    const [loading, setLoading] = useState(true);
    const [btnLoading, setBtnLoading] = useState([])

    const fetchData = async () => {
        const response = await socialService.receivedRequests();
        setReceivedRequests(response.data);

        setBtnLoading((previous) => {
            const filled = Array.from({length: response.data.data.length}, () => false)
            return filled;
        });

        setLoading(false)
    }

    const acceptRequest = async (id, index) => {
        toggleBtnLoading(index);
        const response = await socialService.acceptRequest(id);

        toggleBtnLoading(index);
        setLoading(true);

        props.updateStats()
        fetchData();
    }

    const toggleBtnLoading = (index) => {
        setBtnLoading((previous) => {
            let loadings = [...previous];
            loadings[index] = !previous[index];
            return loadings
        });
    }

    useEffect(() => {
        fetchData();
    }, [])

    return (
        loading ? <Loader /> :receivedRequests.data.length > 0 ? receivedRequests.data.map((item, index) => {
            return <div className="my-2 shadow  text-white bg-dark p-1" id="" key={index}>
                <div className="d-flex justify-content-between">
                    <table className="ms-1">
                        <tr>
                            <td className="align-middle">{item.name}</td>
                            <td className="align-middle">-</td>
                            <td className="align-middle">{item.email}</td>
                            <td className="align-middle">&nbsp;</td>
                        </tr>
                    </table>
                    <div>
                        <button disabled={btnLoading[index]} id="create_request_btn_" className="btn btn-primary me-1" onClick={() => acceptRequest(item.id, index)}>
                            {btnLoading[index] ? 'Accepting...' : 'Accept Request' }
                        </button>
                    </div>
                </div>
            </div>
        }) : <>N/A</>
    )
}
