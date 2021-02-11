import React               from "react";
import ReactDOM            from "react-dom";
import {decodeEntities}    from "@wordpress/html-entities";
import {v4 as uuidv4}      from 'uuid';


const  { __ } = wp.i18n; // Import __() from wp.i18n

/*
 * Print the stars using RaterJs
 */

function YasrCallRaterJs (props) {
    const id   = 'yasr-ranking-element-' + uuidv4();
    const size = window[props.tableId].size;

    return (
        <div id={id} ref={() =>
            raterJs({
                starSize: size,
                step: 0.1,
                showToolTip: false,
                rating: props.rating,
                readOnly: true,
                element: document.getElementById(id),
            })
        }>
        </div>
    );
}

/*
 *
 * Print text after the stars
 * if number of votes is defined, means that is the
 * [yasr_most_or_highest_rated_posts] shortcode
 *
 * @author Dario Curvino <@dudo>
 * @since  2.5.7
 *
 * @param props
 * @param {Object} props.rating  - Object with post attributes
 *
 */

function YasrTextAfterStars (props) {
    //If number_of_votes exists
    if(typeof props.post.number_of_votes !== "undefined") {
        return (
            <span className='yasr-most-rated-text'>
                [{__('Total:', 'yet-another-stars-rating')} {props.post.number_of_votes}
                 &nbsp;&nbsp;
                 {__('Average:', 'yet-another-stars-rating')} {props.post.rating}]
            </span>
        )
    }

    const text = window[props.tableId].custom_text;

    return (
        <span className='yasr-highest-rated-text'>
            {text} {props.post.rating}
        </span>
    );
}

/**
 * Left column for rankings table
 *
 * @author Dario Curvino <@dudo>
 * @since  2.5.7
 *
 * @param props
 * @param {string} props.colClass - Column class name
 * @param {Object} props.post     - Object with post attributes
 *
 * @return {JSX.Element} - html <td> element
 */
function YasrRankingTableLeftColumn (props) {
    return (
        <td className={props.colClass}>
            <a href={props.post.link}>{decodeEntities(props.post.title)}</a>
        </td>
    )
}

/**
* Right column for rankings table
*
* @author Dario Curvino <@dudo>
* @since  2.5.7
*
* @param props
* @param {string} props.colClass - Column class name
* @param {Object} props.post     - Object with post attributes
*
* @return {JSX.Element} - html <td> element
*/
function YasrRankingTableRightColumn (props) {

    const txtPosition = window[props.tableId].text_position;

    if (txtPosition === 'before') {
        return (
            <td className={props.colClass}>
                <YasrTextAfterStars post={props.post} tableId={props.tableId}/>
                <YasrCallRaterJs rating={props.post.rating} tableId={props.tableId}/>
            </td>
        )
    }

    return (
        <td className={props.colClass}>
            <YasrCallRaterJs rating={props.post.rating} tableId={props.tableId}/>
            <YasrTextAfterStars post={props.post} tableId={props.tableId}/>
        </td>
    )
}

/**
 * Print row for Ranking Table
 *
 * @author Dario Curvino <@dudo>
 * @since  2.5.7
 *
 * @param props
 * @param {string} props.source - Source of data
 * @param {Object} props.post     - Object with post attributes
 *
 * @return {JSX.Element} - html <tr> element
 */
function YasrRankingTableRow(props) {
    let leftClass = '';
    let rightClass = '';

    if (props.source === 'overall_rating') {
        leftClass = 'yasr-top-10-overall-left';
        rightClass = 'yasr-top-10-overall-right'
    }
    else if (props.source === 'visitor_votes') {
        leftClass  = 'yasr-top-10-most-highest-left';
        rightClass = 'yasr-top-10-most-highest-right'
    }

    return (
        <tr className={props.trClass}>
            <YasrRankingTableLeftColumn  colClass={leftClass}  post={props.post} />
            <YasrRankingTableRightColumn colClass={rightClass} post={props.post} tableId={props.tableId}/>
        </tr>
    )
}

/**
 * Loop the data array and return the Tbody
 *
 * @author Dario Curvino <@dudo>
 * @since  2.5.7
 *
 * @param props
 * @return {JSX.Element}
 */
function YasrRankingTableRowMap(props) {
    return (
        <tbody id={props.tBodyId} style={{display: props.show}}>
            {
                /*Loop the array, and set the style*/
            }
            {props.data.map(function (post, i) {
                let trClass = 'yasr-rankings-td-colored';
                if(props.source === 'overall_rating') {
                    trClass = 'yasr-rankings-td-white';
                }
                if (i % 2 === 0) {
                    trClass = 'yasr-rankings-td-white';
                    if(props.source === 'overall_rating') {
                        trClass = 'yasr-rankings-td-colored';
                    }
                }

                return(
                    <YasrRankingTableRow
                        key={post.post_id}
                        source={props.source}
                        tableId={props.tableId}
                        post={post}
                        trClass={trClass}
                    />
                )
                })
            }
        </tbody>
    )
}

/**
 * @author Dario Curvino <@dudo>
 * @since  2.5.6
 */
class YasrRanking extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            data: [],
            tableId:      props.tableId,
            source:       props.source,
            queryParams:  window[props.tableId].params
        };
    }

    /**
     * Get data here.
     * Data can come from:
     * rest API if ajax is enabled and no errors are found
     * from a global variable (created with wp_localize_script) retrived from the window object
     * if ajax is disabled or error with rest response are found
     */
    componentDidMount() {
        let data = {};

        //If ajax is disabled, use global value
        if(yasrCommonData.ajaxEnabled !== 'yes') {
            console.info(__('Ajax Disabled, getting data from source', 'yet-another-stars-rating'));
            this.setState({
                isLoaded: true,
                data: window[this.state.tableId]
            });
        }
        else {
            if (this.state.source !== false) {
                const urlYasrRankingApi = this.returnRestUrl();
                Promise.all(urlYasrRankingApi.map((url) =>
                    fetch(url)
                        .then(response => {
                            if (response.ok === true) {
                                return response.json();
                            } else {
                                console.info(__('Ajax Call Failed. Getting data from source'))
                                return 'KO';
                            }
                        })
                        /**
                         * If response is not ok, get data from global var
                         */
                        .then(response => {
                            if (response === 'KO') {
                                data = window[this.state.tableId];
                            } else {
                                if(response.source === 'overall_rating') {
                                    data = response;
                                }
                                //if data is from visitor votes, create an array like this
                                //data[most]
                                //data[highest]
                                if(response.source === 'visitor_votes') {
                                    data[response.show] = response.data_vv
                                }
                            }
                        })
                        .catch((error) => {
                            data = window[this.state.tableId];
                            console.info(__(error));
                        })
                ))
                    //At the end of promise all, data can be from rest api or global var
                    .then(r => {
                        this.setState({
                            isLoaded: true,
                            data: data
                        });
                    })
                    .catch((error) => {
                        console.info(__(error));
                        this.setState({
                            isLoaded: true,
                            data: data
                        });
                });

            } else {
                this.setState({
                    error: __('Invalid Data Source', 'yet-another-stars-rating')
                });
            }
        }
    }

    /*
     * Returns an array with the REST API urls
     *
     * @author Dario Curvino <@dudo>
     * @since  2.5.7
     *
     * @return array of urls
     */
    returnRestUrl(){
        let queryParams       = ((this.state.queryParams !== '') ? this.state.queryParams : '');
        let dataSource        = this.state.source;
        let urlYasrRankingApi = false;
        const yasrRankingsApiPath = 'yet-another-stars-rating/v1/yasr-rankings/';

        if (queryParams !== '') {
            queryParams = '&' + queryParams;
        }

        if(dataSource === 'overall_rating') {
            urlYasrRankingApi = [yasrCommonData.restEndpoint + yasrRankingsApiPath + '?source=' + dataSource + queryParams];
        }
        if(dataSource === 'visitor_votes') {
            let requiredMost    = '';
            let requiredHighest = '';
            if(window[this.state.tableId].required_votes !== '') {
                requiredMost    = '&required_votes=' + window[this.state.tableId].required_votes.most;
                requiredHighest = '&required_votes=' + window[this.state.tableId].required_votes.highest;
            }
            urlYasrRankingApi = [
                yasrCommonData.restEndpoint + yasrRankingsApiPath + '?show=most&source=' + dataSource + queryParams + requiredMost,
                yasrCommonData.restEndpoint + yasrRankingsApiPath + '?show=highest&source=' + dataSource + queryParams + requiredHighest
            ];

        }

        return urlYasrRankingApi;
    }

    /**
     * Print Thead Ranking Table Head
     *
     * @author Dario Curvino <@dudo>
     * @since  2.5.7
     *
     * @return {JSX.Element} - html <thead> element
     */
    rankingTableHead(source) {
        const tableId       = this.state.tableId;
        const idLinkMost    = 'link-most-rated-posts-'+tableId;
        const idLinkHighest = 'link-highest-rated-posts-'+tableId;

        if(source === 'visitor_votes') {
            const defaultView = window[this.state.tableId].view;
            let containerLink = <span>
                                    <span id={idLinkMost}>
                                        {__('Most Rated', 'yet-another-stars-rating')}
                                    </span>&nbsp;|&nbsp;
                                    <a href='#' id={idLinkHighest} onClick={this.switchTBody.bind(this)}>
                                        {__('Highest Rated', 'yet-another-stars-rating')}
                                    </a>
                                 </span>

            if(defaultView === 'highest') {
                containerLink = <span>
                                    <span  id={idLinkHighest} >
                                        {__('Highest Rated', 'yet-another-stars-rating')}
                                    </span>&nbsp;|&nbsp;
                                    <a href='#' id={idLinkMost} onClick={this.switchTBody.bind(this)}>
                                        {__('Most Rated', 'yet-another-stars-rating')}
                                    </a>
                                 </span>
            }

            return (
                <thead>
                    <tr className='yasr-rankings-td-colored yasr-rankings-heading'>
                        <th>Post</th>
                        <th>
                            {__('Order By', 'yet-another-stars-rating-pro')}:&nbsp;&nbsp;
                            {containerLink}
                        </th>
                    </tr>
                </thead>
            )
        }

        return (<></>)
    }

    /**
     * Change style attribute for assigned tbody
     *
     * @author Dario Curvino <@dudo>
     * @since  2.5.7
     *
     */
    switchTBody(event) {
        event.preventDefault();
        const linkId        = event.target.id;

        const tableId       = this.state.tableId;
        const idLinkMost    = 'link-most-rated-posts-'+tableId;
        const idLinkHighest = 'link-highest-rated-posts-'+tableId;
        const bodyIdMost    = 'most-rated-posts-'+tableId;
        const bodyIdHighest = 'highest-rated-posts-'+tableId;

        //change html from a to span and vice versa
        //https://stackoverflow.com/a/13071899/3472877
        let anchor = document.getElementById(linkId);
        let span   = document.createElement("span");

        //Copy innerhtml and id into span element
        span.innerHTML = anchor.innerHTML;
        span.id        = anchor.id;

        //replace <a> with <span>
        anchor.parentNode.replaceChild(span,anchor);

        if(linkId === idLinkMost) {
            //Dispaly body for Most
            document.getElementById(bodyIdHighest).style.display = 'none';
            document.getElementById(bodyIdMost).style.display = '';

            //Here I've to replace <span> with <a>
            span             = document.getElementById(idLinkHighest);
            anchor.innerHTML = span.innerHTML;
            anchor.id        = span.id;
            span.parentNode.replaceChild(anchor,span);
        }
        if(linkId === idLinkHighest) {
            //Dispaly body for Highest
            document.getElementById(bodyIdMost).style.display = 'none';
            document.getElementById(bodyIdHighest).style.display = '';

            //Here I've to replace <span> with <a>
            span             = document.getElementById(idLinkMost);
            anchor.innerHTML = span.innerHTML;
            anchor.id        = span.id;
            span.parentNode.replaceChild(anchor,span);
        }
    }

    /**
     * Print Tbody Ranking Table
     *
     * @author Dario Curvino <@dudo>
     * @since  2.5.6
     *
     * @return {JSX.Element} - html <tbody> element
     */
    rankingTableBody() {
        const {data, source} = this.state;

        if(source === 'overall_rating') {
            return (
                <YasrRankingTableRowMap
                    data={data.data_overall}
                    tableId={this.state.tableId}
                    tBodyId={'overall_'+this.state.tableId}
                    show={'table-row-group'}
                    source={source}
                />
            )
        }

        if(source === 'visitor_votes') {
            const vvMost      = data.most;
            const vvHighest   = data.highest;
            const defaultView = window[this.state.tableId].view
            const display = 'table-row-group';
            const hide    = 'none';

            let styleMost    = display;
            let styleHighest = hide;

            if(defaultView === 'highest') {
                styleMost    = hide;
                styleHighest = display;
            }

            return (
                <>
                    {this.rankingTableHead(source)}
                    <YasrRankingTableRowMap
                        data={vvMost}
                        tableId={this.state.tableId}
                        tBodyId={'most-rated-posts-'+this.state.tableId}
                        show={styleMost}
                        source={source}
                    />
                    <YasrRankingTableRowMap
                        data={vvHighest}
                        tableId={this.state.tableId}
                        tBodyId={'highest-rated-posts-'+this.state.tableId}
                        show={styleHighest}
                        source={source}
                    />
                </>
            )
        }
    }

    /**
     * Render rankings, error should never occour here
     */
    render() {
        const {error, isLoaded} = this.state;
        if(error) {
            return (
                <tbody>
                    <tr>
                        <td>
                        {console.log(error)}
                        Error
                        </td>
                    </tr>
                </tbody>
            )
        } else {
            if (isLoaded === false) {
                return (
                    <tbody>
                    <tr>
                        <td>
                            {__('Loading Charts', 'yet-another-stars-rating')}
                        </td>
                    </tr>
                    </tbody>
                )
            } else {
                return (
                    <>
                        {this.rankingTableBody()}
                    </>
                )
            }
        }
    }
}

export function yasrDrawRankings () {

    //check if there is some shortcode with class yasr-table-chart
    const yasrRankingsInDom = document.getElementsByClassName('yasr-stars-rankings');

    if (yasrRankingsInDom.length > 0) {
        for (let i = 0; i < yasrRankingsInDom.length; i++) {
            const tableId      = yasrRankingsInDom.item(i).id;
            const source       = window[tableId].source;
            const rankingTable = document.getElementById(tableId);

            let dataSource = false
            if(source === 'overall_rating' || source === 'visitor_votes') {
                dataSource = source;
            }

            ReactDOM.render(<YasrRanking source={dataSource} tableId={tableId}/>, rankingTable);
        }
    }

}