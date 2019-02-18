/*!
 * ./om/gulp/gulpfile.babel.js
 *
 * package OM
 * author  Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * link    https://github.com/lucianolaranjeira/om
 * version Beta 2.5.4 • Sunday, February 17, 2019
 */

/*
 |-----------------------------------------------
 | Packs
 |-----------------------------------------------
 */

import '@babel/polyfill';

import { src, dest, series, parallel } from 'gulp';

import merge from 'merge2';
import log from 'fancy-log';
import color from 'cli-color';
import remove from 'del';
import concat from 'gulp-concat';
import rename from 'gulp-rename';
import decomment from 'gulp-decomment';
import less from 'gulp-less';
import sass = from 'gulp-sass';
import minify from 'gulp-clean-css';
import babel from 'gulp-babel';
import uglify from 'gulp-uglify';

/*
 |-----------------------------------------------
 | Build.
 |-----------------------------------------------
 */

const assets = {
    'css': {
        'home': [
            '../dist/assets/css/home.css'
        ],
        'notfound': [
            '../dist/assets/css/notfound.css'
        ]
    },
    'less': {
        'home': [
            '../dist/assets/less/home.less'
        ],
        'notfound': [
            '../dist/assets/less/notfound.less'
        ]
    },
    'scss': {
        'home': [
            '../dist/assets/scss/home.scss'
        ],
        'notfound': [
            '../dist/assets/scss/notfound.scss'
        ]
    },
    'js': {
        'home': [
            '../dist/assets/js/home.js'
        ],
        'notfound': [
            '../dist/assets/js/notfound.js'
        ]
    }
};

const clean = (path) => {

    return remove([`${path}/**`,`!${path}`], {force: true});

}

const build = async (kind) => {

    let streams = merge();

    if (Object.keys(assets[kind]).length > 0) {

        Object.entries(assets[kind]).forEach(([asset, source]) => {

            switch (kind) {

                case 'css':

                    if (source.length) {

                        streams.add(

                            src(source, {allowEmpty: true})
                                .pipe(concat(`${asset}.css`))
                                .pipe(decomment.text())
                                .pipe(minify())
                                .pipe(dest('../dist/public/styles/'))

                        );
                    }

                    break;

                case 'less':

                    if (source.length) {

                        streams.add(

                            src(source, {allowEmpty: true})
                                .pipe(concat(`${asset}.less`))
                                .pipe(less())
                                .pipe(decomment.text())
                                .pipe(minify())
                                .pipe(rename(`${asset}.css`))
                                .pipe(dest('../dist/public/styles/'))

                        );
                    }

                    break;

                case 'scss':

                    if (source.length) {

                        streams.add(

                            src(source, {allowEmpty: true})
                                .pipe(concat(`${asset}.scss`))
                                .pipe(sass())
                                .pipe(decomment.text())
                                .pipe(minify())
                                .pipe(rename(`${asset}.css`))
                                .pipe(dest('../dist/public/styles/'))

                        );
                    }

                    break;

                case 'js':

                    if (source.length) {

                        streams.add(

                            src(source, {allowEmpty: true})
                                .pipe(concat(`${asset}.js`))
                                .pipe(decomment())
                                .pipe(babel({presets: ['@babel/env']}))
                                .pipe(uglify())
                                .pipe(dest('../dist/public/scripts/'))

                        );
                    }

                    break;
            }

        });

    }

    return streams;
};

/*
 |-----------------------------------------------
 | Styles
 |-----------------------------------------------
 */

// clean styles.

export const clean_styles = () => clean('../dist/public/styles');

// build styles.

export const build_css = () => build('css');

export const build_less = () => build('less');

export const build_scss = () => build('scss');

export const build_styles = series(clean_styles, parallel(build_css, build_less, build_scss));

/*
 |-----------------------------------------------
 | Scripts
 |-----------------------------------------------
 */

// clean scripts.

export const clean_scripts = () => clean('../dist/public/scripts');

// build scripts.

export const build_js = () => build('js');

export const build_scripts = series(clean_scripts, parallel(build_js));

/*
 |-----------------------------------------------
 | All.
 |-----------------------------------------------
 */

// clean all.

export const clean_all = parallel(clean_styles, clean_scripts);

// build all.

export const build_all = parallel(build_styles, build_scripts);

/*
 |-----------------------------------------------
 | Menu.
 |-----------------------------------------------
 */

const menu = async () => log(
    '\n\n' +
    '\t'   + 'Please, help yourself:'          + '\n\n' +
    '\t'   + 'To clean:'                       + '\n\n' +
    '\t\t' + color.cyanBright('clean_styles')  + '\n' +
    '\t\t' + color.cyanBright('clean_scripts') + '\n' +
    '\t\t' + color.cyanBright('clean_all')     + '\n\n' +
    '\t'   + 'To build:'                       + '\n\n' +
    '\t\t' + color.cyanBright('build_styles')  + '\n' +
    '\t\t' + color.cyanBright('build_scripts') + '\n' +
    '\t\t' + color.cyanBright('build_all')     + '\n\n' +
    '\t'   + 'For more, visit: ' + color.green('https://github.com/lucianolaranjeira/om') + '\n\n'
);

export default menu;