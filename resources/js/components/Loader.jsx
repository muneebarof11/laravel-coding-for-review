import React from 'react';

export const Loader = () => {
    return (
        <div className="d-flex align-items-center  mb-2  text-white bg-dark p-1 shadow" style={{height: "45px"}}>
            <strong className="ms-1 text-primary">Loading...</strong>
            <div className="spinner-border ms-auto text-primary me-4" role="status" aria-hidden="true"></div>
        </div>
    )
}
