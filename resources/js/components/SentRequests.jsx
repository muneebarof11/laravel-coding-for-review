import React, {useEffect, useState, StrictMode } from 'react';
import socialService from "../services/social.service";
import {Loader} from "./Loader";

export const SentRequests = (props) => {
    const [sentRequests, setSentRequests] = useState({data: []});
    const [loading, setLoading] = useState(true);
    const [btnLoading, setBtnLoading] = useState([])

    const fetchData = async () => {
        const response = await socialService.sentRequests();
        setSentRequests(response.data);

        setBtnLoading((previous) => {
            const filled = Array.from({length: response.data.data.length}, () => false)
            return filled;
        });

        setLoading(false);
    }

    const withdrawRequest = async (id, index) => {
        toggleBtnLoading(index);
        const response = await socialService.withdrawRequest(id);

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
        loading ? <Loader /> : sentRequests.data.length > 0 ? sentRequests.data.map((item, index) => {
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
                        <button disabled={btnLoading[index]} id="create_request_btn_" className="btn btn-danger me-1" onClick={() => withdrawRequest(item.id, index)}>
                            {btnLoading[index] ? 'Removing...' : 'Withdraw Request' }
                        </button>
                    </div>
                </div>
            </div>
        }) : <>N/A</>
    )
}
