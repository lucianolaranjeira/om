/*!
 * .\gulpfile.babel.js
 *
 * package    OM Gulp
 * author     Luciano Laranjeira <inbox@lucianolaranjeira.com>
 * link       https://github.com/lucianolaranjeira/om
 * version    Beta 2.3.1 • Tuesday, December 18, 2018
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
import minify from 'gulp-clean-css';
import babel from 'gulp-babel';
import uglify from 'gulp-uglify';

/*
 |-----------------------------------------------
 | Build.
 |-----------------------------------------------
 */

const assets = {
    'less': {
        'home': [
            '../dist/assets/less/home.less'
        ],
        'notfound': [
            '../dist/assets/less/notfound.less'
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

function clean(path) {

    return remove([`${path}/**`,`!${path}`], {force: true});

}

function build(kind) {

    let streams = merge();

    Object.entries(assets[kind]).forEach(([page, source]) => {

        switch (kind) {

            case 'less':

                if (source.length) {

                    streams.add(

                        src(source, {allowEmpty: true})
                            .pipe(concat(`${page}.less`))
                            .pipe(less())
                            .pipe(decomment.text())
                            .pipe(minify())
                            .pipe(rename(`${page}.css`))
                            .pipe(dest('../dist/public/styles/'))

                    );
                }

                break;

            case 'js':

                if (source.length) {

                    streams.add(

                        src(source, {allowEmpty: true})
                            .pipe(concat(`${page}.js`))
                            .pipe(decomment())
                            .pipe(babel())
                            .pipe(uglify())
                            .pipe(dest('../dist/public/scripts/'))

                    );
                }

                break;
        }

    });

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

export const build_less = () => build('less');

export const build_styles = series(clean_styles, parallel(build_less));

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