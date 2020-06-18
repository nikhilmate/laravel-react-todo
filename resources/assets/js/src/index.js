import React from "react";
import ReactDOM from "react-dom";

const Main = (
    <main>
        <header className="comn__header">
            <div></div>
        </header>
        <section>
            <div className="comn__container">
                <h1>This is Trial</h1>
                <h1>This is Trial 2</h1>
            </div>
        </section>
    </main>
);

if (document.getElementById("main-wrapper")) {
    ReactDOM.render(<Main />, document.getElementById("main-wrapper"));
}
